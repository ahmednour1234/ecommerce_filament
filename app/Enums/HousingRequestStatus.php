<?php

namespace App\Enums;

class HousingRequestStatus
{
    public const UNPAID_SALARY = 'unpaid_salary';
    public const ISSUE = 'issue';
    public const TRANSFER_SPONSORSHIP = 'transfer_sponsorship';
    public const WORK_REFUSED = 'work_refused';
    public const RUNAWAY = 'runaway';
    public const DISPUTE = 'dispute';
    public const READY_FOR_DELIVERY = 'ready_for_delivery';
    public const WITH_CLIENT = 'with_client';
    public const IN_COMPLETION = 'in_completion';
    public const COMPLETED = 'completed';
    public const OUTSIDE_WARRANTY = 'outside_warranty';
    public const INSIDE_WARRANTY = 'inside_warranty';

    public static function options(): array
    {
        return [
            self::UNPAID_SALARY => 'عدم دفع الراتب',
            self::ISSUE => 'مشكلة',
            self::TRANSFER_SPONSORSHIP => 'نقل كفاله',
            self::WORK_REFUSED => 'رفض العمل',
            self::RUNAWAY => 'هروب',
            self::DISPUTE => 'نزاع',
            self::READY_FOR_DELIVERY => 'جاهز للتسليم',
            self::WITH_CLIENT => 'مع العميل',
            self::IN_COMPLETION => 'في الإيواء',
            self::COMPLETED => 'مكتمل',
            self::OUTSIDE_WARRANTY => 'خارج الضمان',
            self::INSIDE_WARRANTY => 'داخل الضمان',
        ];
    }

    public static function labels(): array
    {
        return [
            self::UNPAID_SALARY => 'housing.status.unpaid_salary',
            self::ISSUE => 'housing.status.issue',
            self::TRANSFER_SPONSORSHIP => 'housing.status.transfer_sponsorship',
            self::WORK_REFUSED => 'housing.status.work_refused',
            self::RUNAWAY => 'housing.status.runaway',
            self::DISPUTE => 'housing.status.dispute',
            self::READY_FOR_DELIVERY => 'housing.status.ready_for_delivery',
            self::WITH_CLIENT => 'housing.status.with_client',
            self::IN_COMPLETION => 'housing.status.in_completion',
            self::COMPLETED => 'housing.status.completed',
            self::OUTSIDE_WARRANTY => 'housing.status.outside_warranty',
            self::INSIDE_WARRANTY => 'housing.status.inside_warranty',
        ];
    }

    public static function getLabel(string $status): string
    {
        $translationKey = self::labels()[$status] ?? null;
        if ($translationKey) {
            return tr($translationKey, [], null, 'dashboard') ?: self::options()[$status] ?? $status;
        }
        return self::options()[$status] ?? $status;
    }

    public static function getColor(string $status): string
    {
        return match ($status) {
            self::UNPAID_SALARY, self::WORK_REFUSED, self::RUNAWAY => 'danger',
            self::ISSUE, self::DISPUTE => 'warning',
            self::READY_FOR_DELIVERY, self::COMPLETED => 'success',
            self::TRANSFER_SPONSORSHIP, self::WITH_CLIENT => 'info',
            self::IN_COMPLETION => 'primary',
            self::OUTSIDE_WARRANTY => 'warning',
            self::INSIDE_WARRANTY => 'success',
            default => 'gray',
        };
    }

    public static function values(): array
    {
        return array_keys(self::options());
    }
}
