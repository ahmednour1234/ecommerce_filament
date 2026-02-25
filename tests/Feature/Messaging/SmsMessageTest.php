<?php

namespace Tests\Feature\Messaging;

use App\Models\Messaging\SmsMessage;
use App\Models\Messaging\SmsMessageRecipient;
use App\Models\User;
use App\Services\Messaging\SmsSendService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_sms_creates_records(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $service = app(SmsSendService::class);
        $result = $service->send('+966501234567, +966509876543', 'Test message', $user->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['valid_count']);

        $this->assertDatabaseHas('sms_messages', [
            'created_by' => $user->id,
            'message' => 'Test message',
            'recipients_count' => 2,
            'status' => 'queued',
        ]);

        $smsMessage = SmsMessage::where('created_by', $user->id)->first();
        $this->assertNotNull($smsMessage);

        $recipients = SmsMessageRecipient::where('sms_message_id', $smsMessage->id)->get();
        $this->assertCount(2, $recipients);
    }

    public function test_phone_normalization(): void
    {
        $service = app(SmsSendService::class);
        
        $normalized = $service->normalizePhone('0501234567');
        $this->assertEquals('+966501234567', $normalized);

        $normalized = $service->normalizePhone('966501234567');
        $this->assertEquals('+966501234567', $normalized);
    }

    public function test_phone_validation(): void
    {
        $service = app(SmsSendService::class);
        
        $this->assertTrue($service->validatePhone('+966501234567'));
        $this->assertFalse($service->validatePhone('123'));
        $this->assertFalse($service->validatePhone('12345678901234567'));
    }
}
