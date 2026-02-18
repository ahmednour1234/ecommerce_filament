<?php

namespace App\Filament\Resources\ServiceTransfer\ServiceTransferResource\RelationManagers;

use App\Models\ServiceTransferDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'الوثائق';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file_path')
                    ->label('الملف')
                    ->disk('public')
                    ->directory('service_transfers/documents')
                    ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->maxSize(10240)
                    ->required()
                    ->downloadable()
                    ->previewable()
                    ->openable()
                    ->deletable()
                    ->storeFileNamesIn('file_name')
                    ->visibility('public'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->columns([
                Tables\Columns\TextColumn::make('file_name')
                    ->label('اسم الملف')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('نوع الملف')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('uploadedBy.name')
                    ->label('رفع بواسطة')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الرفع')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $data['service_transfer_id'] = $livewire->ownerRecord->id;
                        $data['uploaded_by'] = auth()->id();
                        
                        // Ensure file_path is a string (not array) and extract file info
                        if (isset($data['file_path'])) {
                            $filePath = is_array($data['file_path']) ? ($data['file_path'][0] ?? null) : $data['file_path'];
                            
                            if ($filePath) {
                                $data['file_path'] = $filePath;
                                // These will be set automatically by model boot, but set them here too for safety
                                if (empty($data['file_name'])) {
                                    $data['file_name'] = basename($filePath);
                                }
                                if (empty($data['file_type'])) {
                                    $data['file_type'] = pathinfo($data['file_name'], PATHINFO_EXTENSION);
                                }
                            }
                        }
                        
                        return $data;
                    })
                    ->visible(fn () => auth()->user()?->can('service_transfers.documents.upload') ?? false),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('تحميل')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (ServiceTransferDocument $record): string => Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()?->can('service_transfers.documents.view') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->after(function (ServiceTransferDocument $record) {
                        if ($record->file_path) {
                            Storage::disk('public')->delete($record->file_path);
                        }
                    })
                    ->visible(fn () => auth()->user()?->can('service_transfers.documents.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function ($records) {
                            foreach ($records as $record) {
                                if ($record->file_path) {
                                    Storage::disk('public')->delete($record->file_path);
                                }
                            }
                        }),
                ]),
            ]);
    }
}
