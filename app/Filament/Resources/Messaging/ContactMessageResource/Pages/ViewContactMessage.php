<?php

namespace App\Filament\Resources\Messaging\ContactMessageResource\Pages;

use App\Filament\Resources\Messaging\ContactMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_read')
                ->label(tr('actions.mark_read', [], null, 'dashboard') ?: 'تحديد كمقروء')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => !$this->record->is_read)
                ->action(fn () => $this->record->update(['is_read' => true]))
                ->requiresConfirmation(),
            Actions\Action::make('mark_unread')
                ->label(tr('actions.mark_unread', [], null, 'dashboard') ?: 'تحديد كغير مقروء')
                ->icon('heroicon-o-x-circle')
                ->visible(fn () => $this->record->is_read)
                ->action(fn () => $this->record->update(['is_read' => false]))
                ->requiresConfirmation(),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete_contact_messages') ?? false),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('معلومات الرسالة')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(tr('forms.contact_messages.name', [], null, 'dashboard') ?: 'الاسم'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label(tr('forms.contact_messages.phone', [], null, 'dashboard') ?: 'الهاتف'),
                        Infolists\Components\TextEntry::make('email')
                            ->label(tr('forms.contact_messages.email', [], null, 'dashboard') ?: 'البريد'),
                        Infolists\Components\TextEntry::make('subject')
                            ->label(tr('forms.contact_messages.subject', [], null, 'dashboard') ?: 'الموضوع'),
                        Infolists\Components\TextEntry::make('is_read')
                            ->label(tr('tables.contact_messages.is_read', [], null, 'dashboard') ?: 'مقروء')
                            ->badge()
                            ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'مقروء' : 'غير مقروء'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('message')
                            ->label(tr('forms.contact_messages.message', [], null, 'dashboard') ?: 'الرسالة')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
