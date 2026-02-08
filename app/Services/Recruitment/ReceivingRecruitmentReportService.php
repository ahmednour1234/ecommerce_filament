<?php

namespace App\Services\Recruitment;

use App\Models\Recruitment\RecruitmentContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReceivingRecruitmentReportService
{
    /**
     * Get base query for received contracts
     */
    public function getBaseQuery(): Builder
    {
        return RecruitmentContract::query()
            ->received()
            ->with(['client', 'worker', 'creator']);
    }

    /**
     * Apply filters to query
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        // Status filter
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Arrival date range
        if (isset($filters['arrival_from']) && $filters['arrival_from']) {
            $query->whereDate('arrival_date', '>=', $filters['arrival_from']);
        }
        if (isset($filters['arrival_until']) && $filters['arrival_until']) {
            $query->whereDate('arrival_date', '<=', $filters['arrival_until']);
        }

        // Trial end date range
        if (isset($filters['trial_from']) && $filters['trial_from']) {
            $query->whereDate('trial_end_date', '>=', $filters['trial_from']);
        }
        if (isset($filters['trial_until']) && $filters['trial_until']) {
            $query->whereDate('trial_end_date', '<=', $filters['trial_until']);
        }

        // Contract end date range
        if (isset($filters['contract_from']) && $filters['contract_from']) {
            $query->whereDate('contract_end_date', '>=', $filters['contract_from']);
        }
        if (isset($filters['contract_until']) && $filters['contract_until']) {
            $query->whereDate('contract_end_date', '<=', $filters['contract_until']);
        }

        // Client filter
        if (isset($filters['client_id']) && $filters['client_id']) {
            $query->where('client_id', $filters['client_id']);
        }

        // Employee filter
        if (isset($filters['created_by']) && $filters['created_by']) {
            $query->where('created_by', $filters['created_by']);
        }

        return $query;
    }

    /**
     * Apply search to query
     */
    public function applySearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
                ->orWhere('contract_no', 'like', "%{$search}%")
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%");
                })
                ->orWhereHas('worker', function ($workerQuery) use ($search) {
                    $workerQuery->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('passport_number', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Get filtered contracts
     */
    public function getContracts(array $filters = [], ?string $search = null): Collection
    {
        $query = $this->getBaseQuery();

        if ($search) {
            $query = $this->applySearch($query, $search);
        }

        $query = $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get contracts query builder (for pagination)
     */
    public function getContractsQuery(array $filters = [], ?string $search = null): Builder
    {
        $query = $this->getBaseQuery();

        if ($search) {
            $query = $this->applySearch($query, $search);
        }

        return $this->applyFilters($query, $filters);
    }

    /**
     * Format contract for export
     */
    public function formatContractForExport(RecruitmentContract $contract): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $contract->id,
            'contract_no' => $contract->contract_no,
            'client' => $locale === 'ar' 
                ? ($contract->client->name_ar ?? '') 
                : ($contract->client->name_en ?? ''),
            'worker' => $contract->worker 
                ? ($locale === 'ar' ? $contract->worker->name_ar : $contract->worker->name_en)
                : '',
            'passport_number' => $contract->worker?->passport_number ?? '',
            'arrival_date' => $contract->arrival_date?->format('Y-m-d') ?? '',
            'trial_end_date' => $contract->trial_end_date?->format('Y-m-d') ?? '',
            'contract_end_date' => $contract->contract_end_date?->format('Y-m-d') ?? '',
            'status' => $this->getStatusLabel($contract->status),
            'employee' => $contract->creator->name ?? '',
        ];
    }

    /**
     * Get status label
     */
    protected function getStatusLabel(string $status): string
    {
        if ($status === 'worker_received') {
            return tr('recruitment.receiving_labor.status.received', [], null, 'dashboard') ?: 'تم الاستلام';
        }
        if ($status === 'pending') {
            return tr('recruitment.receiving_labor.status.pending', [], null, 'dashboard') ?: 'قيد الانتظار';
        }
        if ($status === 'canceled' || $status === 'returned') {
            return tr('recruitment.receiving_labor.status.canceled', [], null, 'dashboard') ?: 'ملغي';
        }
        return $status;
    }

    /**
     * Get export headers
     */
    public function getExportHeaders(): array
    {
        return [
            tr('recruitment.receiving_labor.table.id', [], null, 'dashboard') ?: 'رقم',
            tr('recruitment.receiving_labor.table.client', [], null, 'dashboard') ?: 'العميل',
            tr('recruitment.receiving_labor.table.worker', [], null, 'dashboard') ?: 'اسم العامل',
            tr('recruitment.receiving_labor.table.passport', [], null, 'dashboard') ?: 'رقم الجواز',
            tr('recruitment.receiving_labor.table.arrival_date', [], null, 'dashboard') ?: 'تاريخ الوصول',
            tr('recruitment.receiving_labor.table.trial_end_date', [], null, 'dashboard') ?: 'تاريخ نهاية فترة التجربة',
            tr('recruitment.receiving_labor.table.contract_end_date', [], null, 'dashboard') ?: 'تاريخ نهاية العقد',
            tr('recruitment.receiving_labor.table.status', [], null, 'dashboard') ?: 'حالة الطلب',
            tr('recruitment.receiving_labor.table.employee', [], null, 'dashboard') ?: 'الموظف',
        ];
    }
}
