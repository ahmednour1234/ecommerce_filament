<?php

namespace App\Filament\Pages\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Pages\Page;

class ExpiredContractsPage extends Page
{
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-trash';
    protected static ?string $navigationGroup = 'عقود الاستقدام';
    protected static ?int $navigationSort = 104;
    protected static ?string $navigationTranslationKey = 'recruitment_contract.menu.expired_contracts';
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
