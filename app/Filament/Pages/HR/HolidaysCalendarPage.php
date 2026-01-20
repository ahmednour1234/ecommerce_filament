<?php

namespace App\Filament\Pages\HR;

use App\Models\HR\Holiday;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Actions;
use Filament\Pages\Page;

class HolidaysCalendarPage extends Page
{
    use TranslatableNavigation;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?int $navigationSort = 450;
    protected static string $view = 'filament.pages.hr.holidays-calendar';

    public static function getNavigationLabel(): string
    {
        return tr('navigation.hr_holidays_calendar', [], null, 'dashboard') ?: 'Holidays Calendar';
    }

    public function getTitle(): string
    {
        return tr('navigation.hr_holidays_calendar', [], null, 'dashboard') ?: 'Holidays Calendar';
    }

    public function getHeading(): string
    {
        return tr('navigation.hr_holidays_calendar', [], null, 'dashboard') ?: 'Holidays Calendar';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('hr_holidays.calendar') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('holiday_list')
                ->label(tr('actions.holiday_list', [], null, 'dashboard') ?: 'Holiday List')
                ->icon('heroicon-o-list-bullet')
                ->url(fn () => \App\Filament\Resources\HR\HolidayResource::getUrl('index'))
                ->color('gray'),
            Actions\Action::make('add_new_holiday')
                ->label(tr('actions.add_new_holiday', [], null, 'dashboard') ?: 'Add New Holiday')
                ->icon('heroicon-o-plus')
                ->url(fn () => \App\Filament\Resources\HR\HolidayResource::getUrl('create'))
                ->visible(fn () => auth()->user()?->can('hr_holidays.create') ?? false),
        ];
    }


    /**
     * Check if there are any holidays
     */
    public function hasHolidays(): bool
    {
        return Holiday::count() > 0;
    }
}

