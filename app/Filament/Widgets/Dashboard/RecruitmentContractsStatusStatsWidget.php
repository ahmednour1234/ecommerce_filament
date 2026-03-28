<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Recruitment\RecruitmentContract;
use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecruitmentContractsStatusStatsWidget extends BaseWidget
{
    protected ?string $heading = 'إحصائيات العقود حسب الحالة';
    protected static ?int $sort = 31;
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
            'external_office_approved' => 'موافقة المكتب الخارجي',
            'external_office_accepted' => 'قبول العقد من مكتب خارجي',
            'waiting_bio' => 'انتظار البروف',
            'external_office_accepted2' => 'قبول العقد من مكتب العمل الخارجي',
            'sent_to_embassy' => 'إرسال التأشيرة إلى السفارة السعودية',
            'distinguished' => 'تم التمييز',
            'travel_permit' => 'تصريح سفر',
            'waiting_flight_booking' => 'انتظار حجز تذكرة الطيران',
            'arrival_date' => 'ميعاد الوصول',
            'received' => 'تم الاستلام',
            'return_during_warranty' => 'رجع خلال فترة الضمان',
            'escape' => 'هروب',
        ];

        $stats = [];
        foreach ($statuses as $status) {
            $count = RecruitmentContract::where('status', $status)->count();
            $label = $statusLabels[$status] ?? $status;
            $stats[] = Stat::make($label, $count)
                ->color($status === 'received' ? 'success' : ($status === 'escape' ? 'danger' : 'info'))
                ->url(RecruitmentContractResource::getUrl('index', ['tableFilters' => ['status' => ['value' => $status]]]));
        }
        return $stats;
    }
}
