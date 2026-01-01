<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Accounting\VoucherSignatureResource\Pages;
use App\Models\Accounting\VoucherSignature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class VoucherSignatureResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = VoucherSignature::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?int $navigationSort = 16;
    protected static ?string $navigationGroup = 'Accounting';

    /**
     * Optional: if your TranslatableNavigation trait uses this key
     * for navigation label, keep it.
     */
    protected static ?string $navigationTranslationKey = 'menu.accounting.voucher_signatures';

    // ✅ Group translation (Sidebar group)


    // ✅ Sidebar item label
    public static function getNavigationLabel(): string
    {
        return trans_dash('menu.accounting.voucher_signatures', 'Voucher Signatures');
    }

    // ✅ Resource singular label
    public static function getLabel(): string
    {
        return trans_dash('menu.accounting.voucher_signature', 'Voucher Signature');
    }

    // ✅ Resource plural label
    public static function getPluralLabel(): string
    {
        return trans_dash('menu.accounting.voucher_signatures', 'Voucher Signatures');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(
                trans_dash('vouchers.signatures.basic_info', 'Basic Information')
            )
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(trans_dash('vouchers.signatures.name', 'Name'))
                        ->required()
                        ->maxLength(255)
                        ->helperText(trans_dash(
                            'vouchers.signatures.name_helper',
                            'Display name for the signature (e.g., "Ahmed Nour")'
                        )),

                    Forms\Components\TextInput::make('title')
                        ->label(trans_dash('vouchers.signatures.title', 'Title'))
                        ->maxLength(255)
                        ->nullable()
                        ->helperText(trans_dash(
                            'vouchers.signatures.title_helper',
                            'Role or title (e.g., "Accountant", "Manager")'
                        )),

                    Forms\Components\Select::make('type')
                        ->label(trans_dash('vouchers.signatures.type', 'Type'))
                        ->options([
                            'both'    => trans_dash('vouchers.signatures.type_both', 'Both (Payment & Receipt)'),
                            'receipt' => trans_dash('vouchers.signatures.type_receipt', 'Receipt Only'),
                            'payment' => trans_dash('vouchers.signatures.type_payment', 'Payment Only'),
                        ])
                        ->default('both')
                        ->required()
                        ->helperText(trans_dash(
                            'vouchers.signatures.type_helper',
                            'Restrict signature to specific voucher types.'
                        )),

                    Forms\Components\TextInput::make('sort_order')
                        ->label(trans_dash('vouchers.signatures.sort_order', 'Sort Order'))
                        ->numeric()
                        ->default(0)
                        ->helperText(trans_dash(
                            'vouchers.signatures.sort_order_helper',
                            'Lower numbers appear first in selection lists'
                        )),

                    Forms\Components\Toggle::make('is_active')
                        ->label(trans_dash('vouchers.signatures.active', 'Active'))
                        ->default(true)
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make(
                trans_dash('vouchers.signatures.signature_image', 'Signature Image')
            )
                ->schema([
                    Forms\Components\FileUpload::make('image_path')
                        ->label(trans_dash('vouchers.signatures.image', 'Signature Image'))
                        ->image()
                        ->directory('voucher-signatures')
                        ->disk('public')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->imageEditor()
                        ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
                        ->nullable()
                        ->helperText(trans_dash(
                            'vouchers.signatures.image_helper',
                            'Upload signature image or stamp. Recommended size: 200x100px'
                        ))
                        ->columnSpanFull()
                        ->afterStateUpdated(function ($state, $record) {
                            if (! $record) {
                                return;
                            }

                            if ($record->image_path && $state && $state !== $record->image_path) {
                                Storage::disk('public')->delete($record->image_path);
                            }
                        }),

                    Forms\Components\ViewField::make('image_preview')
                        ->view('filament.forms.components.signature-preview')
                        ->visible(fn ($record) => filled($record?->image_path))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans_dash('vouchers.signatures.name', 'Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label(trans_dash('vouchers.signatures.title', 'Title'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(trans_dash('vouchers.signatures.type', 'Type'))
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'receipt' => trans_dash('vouchers.signatures.type_receipt', 'Receipt'),
                        'payment' => trans_dash('vouchers.signatures.type_payment', 'Payment'),
                        default   => trans_dash('vouchers.signatures.type_both', 'Both'),
                    })
                    ->colors([
                        'success' => 'both',
                        'warning' => 'receipt',
                        'danger'  => 'payment',
                    ])
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans_dash('vouchers.signatures.active', 'Active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(trans_dash('vouchers.signatures.sort_order', 'Sort Order'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label(trans_dash('vouchers.signatures.image', 'Image'))
                    ->disk('public')
                    ->circular()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans_dash('tables.common.created_at', 'Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(trans_dash('vouchers.signatures.type', 'Type'))
                    ->options([
                        'both'    => trans_dash('vouchers.signatures.type_both', 'Both'),
                        'receipt' => trans_dash('vouchers.signatures.type_receipt', 'Receipt'),
                        'payment' => trans_dash('vouchers.signatures.type_payment', 'Payment'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans_dash('vouchers.signatures.active', 'Active'))
                    ->placeholder(trans_dash('common.all', 'All'))
                    ->trueLabel(trans_dash('common.active_only', 'Active only'))
                    ->falseLabel(trans_dash('common.inactive_only', 'Inactive only')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('voucher_signatures.update') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('voucher_signatures.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label(trans_dash('common.activate', 'Activate'))
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn () => auth()->user()?->can('voucher_signatures.update') ?? false),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label(trans_dash('common.deactivate', 'Deactivate'))
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn () => auth()->user()?->can('voucher_signatures.update') ?? false),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('voucher_signatures.delete') ?? false),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVoucherSignatures::route('/'),
            'create' => Pages\CreateVoucherSignature::route('/create'),
            'edit'   => Pages\EditVoucherSignature::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('voucher_signatures.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('voucher_signatures.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('voucher_signatures.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('voucher_signatures.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
