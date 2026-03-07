<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Services\Recruitment\RecruitmentContractService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecruitmentContract extends EditRecord
{
    protected static string $resource = RecruitmentContractResource::class;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        $url = parent::getUrl($parameters, $isAbsolute, $panel, $tenant);
        return static::addPublicToUrl($url);
    }

    protected static function addPublicToUrl(string $url): string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        
        if (str_contains($path, '/admin/') && !str_contains($path, '/public/admin/')) {
            if (str_starts_with($path, '/public/')) {
                $path = substr($path, 7);
            }
            $newPath = str_replace('/admin/', '/public/admin/', $path);
            
            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? '';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
            
            return $scheme . '://' . $host . $newPath . $query . $fragment;
        }
        
        return $url;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $oldStatus = $this->record->status;
        $newStatus = $data['status'] ?? null;
        $statusDate = $data['status_date'] ?? null;
        $allStatusDates = json_decode($data['all_status_dates'] ?? '{}', true) ?? [];

        $service = app(RecruitmentContractService::class);

        // Update status if changed
        if ($newStatus && $oldStatus !== $newStatus) {
            $service->updateStatus($this->record, $newStatus, null, $statusDate);
        }

        // Update/create statusLogs for all statuses with dates
        foreach ($allStatusDates as $status => $date) {
            if (empty($date)) {
                continue;
            }

            // Check if statusLog exists for this status
            $existingLog = $this->record->statusLogs()
                ->where('new_status', $status)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingLog) {
                // Update existing log if date changed
                if ($existingLog->status_date !== $date) {
                    $existingLog->update(['status_date' => $date]);
                }
            } else {
                // Create new log if doesn't exist
                $previousStatus = $this->record->statusLogs()
                    ->where('new_status', '!=', $status)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                $oldStatusForLog = $previousStatus ? $previousStatus->new_status : null;
                $service->logStatusChange($this->record, $oldStatusForLog, $status, null, $date);
            }
        }

        unset($data['status_date'], $data['all_status_dates']);
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
