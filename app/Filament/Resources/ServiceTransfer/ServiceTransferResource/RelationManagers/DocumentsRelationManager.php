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
                    ->deletable(),
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
                        
                        if (isset($data['file_path'])) {
                            // Handle both array and string formats
                            $filePath = is_array($data['file_path']) ? ($data['file_path'][0] ?? null) : $data['file_path'];
                            
                            if ($filePath) {
                                $data['file_path'] = $filePath;
                                $fileName = basename($filePath);
                                $data['file_name'] = $fileName;
                                $data['file_type'] = pathinfo($fileName, PATHINFO_EXTENSION);
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
