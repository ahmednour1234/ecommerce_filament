<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Recruitment\RecruitmentContract;
use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecruitmentContractsStatusStatsWidget extends BaseWidget
{
    protected ?string $heading = null;
    protected static ?int $sort = 22;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment_contracts.view_any');
    }

    protected function getStats(): array
    {
        // جلب كل الحالات الفعلية من قاعدة البيانات
        $statuses = RecruitmentContract::query()
            ->select('status')
            ->distinct()
            ->pluck('status')
            ->filter();

        $statusLabels = [
            'new' => 'جديد',
            'external_office_approval' => 'بانتظار موافقة المكتب الخارجي',
            'external_office_approved' => 'موافقة المكتب الخارجي',
            'contract_accepted_external_office' => 'قبول العقد من المكتب الخارجي',
            'external_office_accepted' => 'قبول العقد من مكتب خارجي',
            'waiting_approval' => 'انتظار الموافقة',
            'waiting_bio' => 'انتظار البروف',
            'contract_accepted_labor_ministry' => 'قبول العقد من وزارة العمل',
            'external_office_accepted2' => 'قبول العقد من مكتب العمل الخارجي',
            'sent_to_saudi_embassy' => 'إرسال التأشيرة إلى السفارة السعودية',
            'visa_issued' => 'إصدار التأشيرة',
            'visa_cancelled' => 'إلغاء التفييز',
            'distinguished' => 'تم التمييز',
            'travel_permit' => 'تصريح سفر',
            'travel_permit_after_visa_issued' => 'تصريح سفر بعد تم التفييز',
            'waiting_flight_booking' => 'انتظار حجز تذكرة الطيران',
            'arrival_scheduled' => 'معاد الوصول',
            'arrival_date' => 'ميعاد الوصول',
            'received' => 'تم الاستلام',
            'return_during_warranty' => 'رجع خلال فترة الضمان',
            'escape' => 'هروب',
            'runaway' => 'هروب',
        ];

        $stats = [];
        foreach ($statuses as $status) {
            $count = RecruitmentContract::where('status', $status)->count();
            $label = $statusLabels[$status] ?? str_replace('_', ' ', $status);
            $label = preg_replace_callback('/\b([a-z])/u', fn($m) => mb_strtoupper($m[1], 'UTF-8'), $label); // أول حرف كبير
            $label = strtr($label, [
                'new' => 'جديد',
                'received' => 'تم الاستلام',
                'escape' => 'هروب',
            ]);
            $stats[] = Stat::make($label, $count)
                ->color($status === 'received' ? 'success' : ($status === 'escape' ? 'danger' : 'info'))
                ->url(RecruitmentContractResource::getUrl('index', ['tableFilters' => ['status' => ['value' => $status]]]));
        }
        return $stats;
    }
}
