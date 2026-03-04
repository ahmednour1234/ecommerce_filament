<?php

namespace App\Filament\Pages\Housing;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\AccommodationEntry;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class HousingAlertsPage extends Page implements HasTable
{
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationGroup = 'إيواء الاستقدام';
    protected static ?string $navigationLabel = 'تنبيهات الايواء';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.housing.alerts';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('housing.accommodation_entries.view_any') ?? false;
    }

    public function getTitle(): string
    {
        return tr('housing.alerts.title', [], null, 'dashboard') ?: 'تنبيهات الايواء';
    }

    public function getHeading(): string
    {
        return tr('housing.alerts.title', [], null, 'dashboard') ?: 'تنبيهات الايواء';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getAlertsQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('رقم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn ($state) => $state === 'recruitment' ? 'إيواء الاستقدام' : 'إيواء التأجير')
                    ->badge()
                    ->color(fn ($state) => $state === 'recruitment' ? 'success' : 'warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label('اسم العامل')
                    ->formatStateUsing(fn ($state, $record) => $record->laborer 
                        ? (app()->getLocale() === 'ar' ? $record->laborer->name_ar : $record->laborer->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract_no')
                    ->label('رقم العقد')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status.name')
                    ->label('الحالة الحالية')
                    ->formatStateUsing(fn ($state, $record) => $record->status 
                        ? (app()->getLocale() === 'ar' ? $record->status->name_ar : $record->status->name_en)
                        : '')
                    ->badge()
                    ->color(fn ($record) => $record->status?->color ?? 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_status_update')
                    ->label('آخر تحديث للحالة')
                    ->formatStateUsing(function ($record) {
                        $lastLog = $record->statusLogs()
                            ->where('new_status_id', $record->status_id)
                            ->orderBy('status_date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->first();
                        
                        if ($lastLog) {
                            return $lastLog->status_date 
                                ? Carbon::parse($lastLog->status_date)->format('Y-m-d')
                                : $lastLog->created_at->format('Y-m-d');
                        }
                        
                        return $record->entry_date ? $record->entry_date->format('Y-m-d') : '-';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('days_since_update')
                    ->label('عدد الأيام')
                    ->formatStateUsing(function ($record) {
                        $lastLog = $record->statusLogs()
                            ->where('new_status_id', $record->status_id)
                            ->orderBy('status_date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->first();
                        
                        $lastUpdateDate = null;
                        if ($lastLog) {
                            $lastUpdateDate = $lastLog->status_date 
                                ? Carbon::parse($lastLog->status_date)
                                : $lastLog->created_at;
                        } else {
                            $lastUpdateDate = $record->entry_date ? Carbon::parse($record->entry_date) : null;
                        }
                        
                        if ($lastUpdateDate) {
                            return Carbon::now()->diffInDays($lastUpdateDate) . ' يوم';
                        }
                        
                        return '-';
                    })
                    ->badge()
                    ->color(fn ($record) => {
                        $lastLog = $record->statusLogs()
                            ->where('new_status_id', $record->status_id)
                            ->orderBy('status_date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->first();
                        
                        $lastUpdateDate = null;
                        if ($lastLog) {
                            $lastUpdateDate = $lastLog->status_date 
                                ? Carbon::parse($lastLog->status_date)
                                : $lastLog->created_at;
                        } else {
                            $lastUpdateDate = $record->entry_date ? Carbon::parse($record->entry_date) : null;
                        }
                        
                        if ($lastUpdateDate) {
                            $days = Carbon::now()->diffInDays($lastUpdateDate);
                            if ($days > 7) {
                                return 'danger';
                            } elseif ($days > 3) {
                                return 'warning';
                            }
                        }
                        
                        return 'success';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('building.name_ar')
                    ->label('المبنى')
                    ->formatStateUsing(fn ($state, $record) => $record->building 
                        ? (app()->getLocale() === 'ar' ? $record->building->name_ar : $record->building->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('entry_date')
                    ->label('تاريخ الدخول')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'recruitment' => 'إيواء الاستقدام',
                        'rental' => 'إيواء التأجير',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->where('type', $data['value']);
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('status_id')
                    ->label('الحالة')
                    ->relationship('status', 'name_ar')
                    ->searchable(),
            ])
            ->defaultSort('days_since_update', 'desc')
            ->emptyStateHeading('لا توجد تنبيهات')
            ->emptyStateDescription('جميع حالات الإيواء محدثة');
    }

    protected function getAlertsQuery(): Builder
    {
        $oneWeekAgo = Carbon::now()->subWeek();

        // Get all entries with status that haven't exited
        $entries = AccommodationEntry::query()
            ->whereNotNull('status_id')
            ->whereNull('exit_date')
            ->with(['laborer', 'status', 'building', 'statusLogs'])
            ->get();

        // Filter entries where last status update is more than a week ago
        $alertEntryIds = $entries->filter(function ($entry) use ($oneWeekAgo) {
            // Get the last log for the current status
            $lastLog = $entry->statusLogs
                ->where('new_status_id', $entry->status_id)
                ->sortByDesc(function ($log) {
                    return $log->status_date ? $log->status_date->format('Y-m-d H:i:s') : $log->created_at->format('Y-m-d H:i:s');
                })
                ->first();
            
            if ($lastLog) {
                $lastUpdateDate = $lastLog->status_date 
                    ? Carbon::parse($lastLog->status_date)
                    : $lastLog->created_at;
                
                return $lastUpdateDate->lt($oneWeekAgo);
            }
            
            // If no log exists for current status, check entry_date
            if ($entry->entry_date) {
                return Carbon::parse($entry->entry_date)->lt($oneWeekAgo);
            }
            
            return false;
        })->pluck('id')->toArray();

        // Return query with filtered IDs (empty array returns no results)
        if (empty($alertEntryIds)) {
            return AccommodationEntry::query()->whereRaw('1 = 0');
        }

        return AccommodationEntry::query()
            ->whereIn('id', $alertEntryIds)
            ->with(['laborer', 'status', 'building', 'statusLogs']);
    }
}
