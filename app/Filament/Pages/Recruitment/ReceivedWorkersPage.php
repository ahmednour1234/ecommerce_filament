<?php

namespace App\Filament\Pages\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Pages\Page;

class ReceivedWorkersPage extends Page
{
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationGroup = 'عقود الاستقدام';
    protected static ?string $navigationLabel = 'العمالة المستلمة';
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        $recruitmentTypes = [
            \App\Models\User::TYPE_CUSTOMER_SERVICE,
            \App\Models\User::TYPE_COORDINATOR,
            \App\Models\User::TYPE_ACCOUNTANT,
            \App\Models\User::TYPE_GENERAL_ACCOUNTANT,
        ];
        return in_array($user->type, $recruitmentTypes, true) || $user->can('recruitment_contracts.view_any');
    }
    protected static string $view = 'filament.pages.recruitment.received-workers';

    protected static ?string $title = null;

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.recruitment_contracts.received_workers', [], null, 'dashboard') ?: 'العمالة المستلمة';
    }

    public function mount(): void
    {
        $this->redirect(RecruitmentContractResource::getUrl('index', [
            'tableFilters' => [
                'status' => ['value' => 'worker_received']
            ]
        ]), navigate: true);
    }
}
