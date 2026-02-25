<?php

namespace App\Services\Messaging;

use App\Models\Messaging\MessageContact;
use App\Models\Messaging\SmsMessage;
use App\Models\Messaging\SmsMessageRecipient;
use App\Models\MainCore\SmsSetting;
use App\Models\Sales\Customer;
use Illuminate\Support\Collection;

class SmsSendService
{
    public function send(string $recipients, string $message, ?int $userId = null): array
    {
        $normalizedPhones = $this->parseAndNormalizeRecipients($recipients);
        
        if (empty($normalizedPhones['valid'])) {
            return [
                'success' => false,
                'message' => 'لا توجد أرقام صحيحة للإرسال',
                'valid_count' => 0,
                'invalid_count' => count($normalizedPhones['invalid']),
                'duplicates_removed' => $normalizedPhones['duplicates_removed'],
            ];
        }

        $dailyLimit = (int) SmsSetting::getValue('daily_limit', 500);
        $isSendingEnabled = SmsSetting::getValue('is_sending_enabled', 'true') === 'true';

        if (!$isSendingEnabled) {
            return [
                'success' => false,
                'message' => 'الإرسال معطل حالياً',
                'valid_count' => 0,
                'invalid_count' => 0,
                'duplicates_removed' => 0,
            ];
        }

        if (count($normalizedPhones['valid']) > $dailyLimit) {
            return [
                'success' => false,
                'message' => "عدد المستلمين (" . count($normalizedPhones['valid']) . ") يتجاوز الحد اليومي ({$dailyLimit})",
                'valid_count' => 0,
                'invalid_count' => 0,
                'duplicates_removed' => 0,
            ];
        }

        $smsMessage = SmsMessage::create([
            'created_by' => $userId ?? auth()->id(),
            'message' => $message,
            'recipients_count' => count($normalizedPhones['valid']),
            'status' => 'queued',
            'meta' => [
                'invalid_numbers' => $normalizedPhones['invalid'],
                'duplicates_removed' => $normalizedPhones['duplicates_removed'],
            ],
        ]);

        foreach ($normalizedPhones['valid'] as $phone) {
            SmsMessageRecipient::create([
                'sms_message_id' => $smsMessage->id,
                'phone' => $phone,
                'status' => 'queued',
            ]);
        }

        return [
            'success' => true,
            'message' => "تم إنشاء الرسالة بنجاح. عدد المستلمين: " . count($normalizedPhones['valid']),
            'sms_message_id' => $smsMessage->id,
            'valid_count' => count($normalizedPhones['valid']),
            'invalid_count' => count($normalizedPhones['invalid']),
            'duplicates_removed' => $normalizedPhones['duplicates_removed'],
        ];
    }

    public function parseAndNormalizeRecipients(string $recipients): array
    {
        $phones = preg_split('/[,\n\r\s]+/', $recipients);
        $phones = array_filter(array_map('trim', $phones));
        
        $normalized = [];
        $invalid = [];
        $seen = [];

        foreach ($phones as $phone) {
            if (empty($phone)) {
                continue;
            }

            $normalizedPhone = $this->normalizePhone($phone);
            
            if (!$this->validatePhone($normalizedPhone)) {
                $invalid[] = $phone;
                continue;
            }

            if (isset($seen[$normalizedPhone])) {
                continue;
            }

            $seen[$normalizedPhone] = true;
            $normalized[] = $normalizedPhone;
        }

        $duplicatesRemoved = max(0, count($phones) - count($normalized) - count($invalid));

        return [
            'valid' => $normalized,
            'invalid' => $invalid,
            'duplicates_removed' => max(0, $duplicatesRemoved),
        ];
    }

    public function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        
        $hasPlus = str_starts_with($phone, '+');
        
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        if (!$hasPlus && str_starts_with($phone, '0')) {
            $phone = '+966' . substr($phone, 1);
        } elseif (!$hasPlus && !str_starts_with($phone, '966')) {
            $phone = '+966' . $phone;
        } elseif (!$hasPlus) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    public function validatePhone(string $phone): bool
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        $length = strlen($phone);
        
        return $length >= 7 && $length <= 15;
    }

    public function importCustomers(): array
    {
        $customers = Customer::whereNotNull('phone')
            ->where('is_active', true)
            ->get();

        $imported = 0;
        $skipped = 0;

        foreach ($customers as $customer) {
            $normalizedPhone = $this->normalizePhone($customer->phone);
            
            if (!$this->validatePhone($normalizedPhone)) {
                $skipped++;
                continue;
            }

            $exists = MessageContact::where('phone', $normalizedPhone)
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            MessageContact::create([
                'name_ar' => $customer->name,
                'phone' => $normalizedPhone,
                'source' => 'customers_import',
            ]);

            $imported++;
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'total' => $customers->count(),
        ];
    }
}
