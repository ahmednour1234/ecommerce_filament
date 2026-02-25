<?php

namespace App\Filament\Resources\Messaging\SmsMessageResource\Pages;

use App\Filament\Resources\Messaging\SmsMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewSmsMessage extends ViewRecord
{
    protected static string $resource = SmsMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete_sms_messages') ?? false),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('معلومات الرسالة')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('الرقم'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('createdBy.name')
                            ->label('المرسل'),
                        Infolists\Components\TextEntry::make('recipients_count')
                            ->label('عدد المستلمين'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'queued' => 'warning',
                                'sent' => 'success',
                                'failed' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'queued' => tr('status.queued', [], null, 'dashboard') ?: 'قيد الإرسال',
                                'sent' => tr('status.sent', [], null, 'dashboard') ?: 'تم الإرسال',
                                'failed' => tr('status.failed', [], null, 'dashboard') ?: 'فشل',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('message')
                            ->label('الرسالة')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('قائمة المستلمين')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('recipients')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('phone')
                                    ->label('الرقم'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('الحالة')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'queued' => 'warning',
                                        'sent' => 'success',
                                        'failed' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'queued' => tr('status.queued', [], null, 'dashboard') ?: 'قيد الإرسال',
                                        'sent' => tr('status.sent', [], null, 'dashboard') ?: 'تم الإرسال',
                                        'failed' => tr('status.failed', [], null, 'dashboard') ?: 'فشل',
                                        default => $state,
                                    }),
                                Infolists\Components\TextEntry::make('error')
                                    ->label('خطأ')
                                    ->visible(fn ($record) => !empty($record->error)),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
