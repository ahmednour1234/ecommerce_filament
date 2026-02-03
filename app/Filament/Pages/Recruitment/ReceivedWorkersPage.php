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
    protected static ?int $navigationSort = 103;
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('recruitment_contracts.view_any') ?? false;
    }
    protected static ?string $navigationTranslationKey = 'recruitment_contract.menu.received_workers';
    protected static string $view = 'filament.pages.recruitment.received-workers';

    protected static ?string $title = null;

    public static function getNavigationLabel(): string
    {
        return tr('recruitment_contract.menu.received_workers', [], null, 'dashboard') ?: 'العمالة المستلمة';
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
