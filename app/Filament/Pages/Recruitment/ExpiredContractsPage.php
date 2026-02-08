<?php

namespace App\Filament\Pages\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Pages\Page;

class ExpiredContractsPage extends Page
{
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-trash';
    protected static ?string $navigationGroup = 'recruitment_contracts';
    protected static ?int $navigationSort = 12;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment_contracts.expired_contracts';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('recruitment_contracts.view_any') ?? false;
    }
    protected static string $view = 'filament.pages.recruitment.expired-contracts';

    protected static ?string $title = null;

    public static function getNavigationLabel(): string
    {
        return tr('recruitment_contract.menu.expired_contracts', [], null, 'dashboard') ?: 'العقود المنتهية';
    }

    public function mount(): void
    {
        $this->redirect(RecruitmentContractResource::getUrl('index', [
            'tableFilters' => [
                'status' => ['value' => 'closed']
            ]
        ]), navigate: true);
    }
}
