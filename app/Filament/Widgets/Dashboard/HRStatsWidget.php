<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\HR\AttendanceDay;
use App\Models\HR\Employee;
use App\Models\HR\ExcuseRequest;
use App\Models\HR\LeaveRequest;
use App\Models\HR\Loan;
use App\Models\HR\LoanInstallment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class HRStatsWidget extends BaseWidget
{
    public ?string $from = null;
    public ?string $to = null;
    public ?int $branch_id = null;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $dateRange = session()->get('dashboard_date_range', 'month');
        $dateFrom = session()->get('dashboard_date_from');
        $dateTo = session()->get('dashboard_date_to');
        
        if ($dateRange === 'today') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($dateRange === 'month') {
            $from = now()->startOfMonth()->startOfDay();
            $to = now()->endOfDay();
        } else {
            $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->startOfMonth()->startOfDay();
            $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfDay();
        }
        
        $user = auth()->user();
        $branchId = $user->branch_id ?? $this->branch_id ?? null;

        $cacheKey = "dashboard_hr_stats_{$branchId}_{$from->toDateString()}_{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId) {
            $stats = [];

            try {
                $employeeQuery = Employee::query();
                if ($branchId) {
                    $employeeQuery->where('branch_id', $branchId);
                }
                $totalEmployees = $employeeQuery->active()->count();
                $stats[] = Stat::make('عدد الموظفين', Number::format($totalEmployees))
                    ->description('الموظفون النشطون')
                    ->descriptionIcon('heroicon-o-users')
                    ->color('success')
                    ->icon('heroicon-o-user-group');
            } catch (\Exception $e) {
                $stats[] = Stat::make('عدد الموظفين', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            try {
                $newEmployeesQuery = Employee::query()
                    ->whereMonth('hire_date', now()->month)
                    ->whereYear('hire_date', now()->year);
                if ($branchId) {
                    $newEmployeesQuery->where('branch_id', $branchId);
                }
                $newEmployeesThisMonth = $newEmployeesQuery->count();
                $stats[] = Stat::make('عدد الموظفين الجدد هذا الشهر', Number::format($newEmployeesThisMonth))
                    ->description('تم تعيينهم هذا الشهر')
                    ->descriptionIcon('heroicon-o-user-plus')
                    ->color('info')
                    ->icon('heroicon-o-user-plus');
            } catch (\Exception $e) {
                $stats[] = Stat::make('عدد الموظفين الجدد هذا الشهر', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            try {
                $attendanceQuery = AttendanceDay::query()->whereDate('date', today());
                if ($branchId && Employee::where('branch_id', $branchId)->exists()) {
                    $employeeIds = Employee::where('branch_id', $branchId)->pluck('id');
                    $attendanceQuery->whereIn('employee_id', $employeeIds);
                }
                $presentToday = $attendanceQuery->clone()->present()->count();
                $absentToday = $attendanceQuery->clone()->absent()->count();
                
                if ($presentToday > 0 || $absentToday > 0) {
                    $stats[] = Stat::make('الحضور اليوم', Number::format($presentToday) . ' / ' . Number::format($absentToday))
                        ->description('حاضر / غائب')
                        ->descriptionIcon('heroicon-o-calendar')
                        ->color('success')
                        ->icon('heroicon-o-check-circle');
                } else {
                    $stats[] = Stat::make('الحضور اليوم', 'غير متاح')
                        ->description('لا توجد بيانات')
                        ->color('gray')
                        ->icon('heroicon-o-x-circle');
                }
            } catch (\Exception $e) {
                $stats[] = Stat::make('الحضور اليوم', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            try {
                $leaveQuery = LeaveRequest::query();
                if ($branchId && Employee::where('branch_id', $branchId)->exists()) {
                    $employeeIds = Employee::where('branch_id', $branchId)->pluck('id');
                    $leaveQuery->whereIn('employee_id', $employeeIds);
                }
                $pendingLeaves = $leaveQuery->clone()->pending()->count();
                $approvedLeaves = $leaveQuery->clone()->approved()->count();
                $rejectedLeaves = $leaveQuery->clone()->rejected()->count();
                
                $stats[] = Stat::make('عدد طلبات الإجازات', Number::format($pendingLeaves + $approvedLeaves + $rejectedLeaves))
                    ->description("معلق: {$pendingLeaves} | معتمد: {$approvedLeaves} | مرفوض: {$rejectedLeaves}")
                    ->descriptionIcon('heroicon-o-calendar-days')
                    ->color('warning')
                    ->icon('heroicon-o-document-text');
            } catch (\Exception $e) {
                $stats[] = Stat::make('عدد طلبات الإجازات', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            try {
                $excuseQuery = ExcuseRequest::query()->whereBetween('date', [$from, $to]);
                if ($branchId && Employee::where('branch_id', $branchId)->exists()) {
                    $employeeIds = Employee::where('branch_id', $branchId)->pluck('id');
                    $excuseQuery->whereIn('employee_id', $employeeIds);
                }
                $excuseCount = $excuseQuery->count();
                
                $stats[] = Stat::make('عدد الاستئذانات', Number::format($excuseCount))
                    ->description('في الفترة المحددة')
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('info')
                    ->icon('heroicon-o-clock');
            } catch (\Exception $e) {
                $stats[] = Stat::make('عدد الاستئذانات', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            try {
                $loanQuery = Loan::query()->active();
                if ($branchId && Employee::where('branch_id', $branchId)->exists()) {
                    $employeeIds = Employee::where('branch_id', $branchId)->pluck('id');
                    $loanQuery->whereIn('employee_id', $employeeIds);
                }
                
                $activeLoans = $loanQuery->get();
                $totalActiveLoans = $activeLoans->sum('base_amount');
                
                $remainingAmount = 0;
                foreach ($activeLoans as $loan) {
                    $paidAmount = LoanInstallment::where('loan_id', $loan->id)
                        ->where('status', 'paid')
                        ->sum('amount');
                    $remainingAmount += ($loan->base_amount - $paidAmount);
                }
                
                $stats[] = Stat::make('إجمالي القروض النشطة', Number::currency($totalActiveLoans))
                    ->description('المتبقي: ' . Number::currency($remainingAmount))
                    ->descriptionIcon('heroicon-o-banknotes')
                    ->color('danger')
                    ->icon('heroicon-o-currency-dollar');
            } catch (\Exception $e) {
                $stats[] = Stat::make('إجمالي القروض النشطة', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            return $stats;
        });
    }
}
