<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Recruitment\RecruitmentContract;
use App\Services\Recruitment\RecruitmentContractService;
use Filament\Resources\Pages\CreateRecord;

class CreateRecruitmentContract extends CreateRecord
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['current_section'] = RecruitmentContract::SECTION_CUSTOMER_SERVICE;
        if (! auth()->user()?->can('recruitment_contracts.assign_employee_branch')) {
            $data['branch_id'] = auth()->user()?->branch_id;
            $data['marketer_id'] = auth()->user()?->employee?->id;
        }
        $allStatusDates = json_decode($data['all_status_dates'] ?? '{}', true) ?? [];
        $status = $data['status'] ?? 'new';
        $statusDate = $data['status_date'] ?? null;

        unset($data['status_date'], $data['all_status_dates']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $allStatusDates = json_decode($this->form->getState()['all_status_dates'] ?? '{}', true) ?? [];
        $status = $this->record->status;
        $statusDate = $this->form->getState()['status_date'] ?? null;

        $service = app(RecruitmentContractService::class);

        // Create statusLog for current status
        if ($statusDate) {
            $service->logStatusChange($this->record, null, $status, null, $statusDate);
        }

        // Create statusLogs for all statuses with dates
        foreach ($allStatusDates as $statusKey => $date) {
            if (empty($date) || $statusKey === $status) {
                continue;
            }

            $previousStatus = $this->record->statusLogs()
                ->where('new_status', '!=', $statusKey)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $oldStatusForLog = $previousStatus ? $previousStatus->new_status : null;
            $service->logStatusChange($this->record, $oldStatusForLog, $statusKey, null, $date);
        }
    }
}
