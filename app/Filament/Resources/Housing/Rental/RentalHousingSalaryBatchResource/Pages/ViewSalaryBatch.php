<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingSalaryBatchResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingSalaryBatchResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;

class ViewSalaryBatch extends ViewRecord
{
    protected static string $resource = RentalHousingSalaryBatchResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->items()->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label('اسم العامل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('الراتب الأساسي')
                    ->money('SAR'),
                Tables\Columns\TextColumn::make('deductions_total')
                    ->label('الخصومات')
                    ->money('SAR'),
                Tables\Columns\TextColumn::make('net_salary')
                    ->label('صافي الراتب')
                    ->money('SAR'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                    ]),
            ]);
    }
}
