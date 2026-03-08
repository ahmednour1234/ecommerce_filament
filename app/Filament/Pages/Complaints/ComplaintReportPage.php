<?php

namespace App\Filament\Pages\Complaints;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Complaint;
use App\Models\MainCore\Branch;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class ComplaintReportPage extends Page
{
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = null;
    protected static ?string $navigationLabel = 'تقرير الشكاوي';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.complaints.complaint-report';

    public $branch_id = null;
    public $from_date = null;
    public $to_date = null;

    public function getTitle(): string
    {
        return tr('complaint.report.title', [], null, 'dashboard') ?: 'تقرير الشكاوي';
    }

    public function getHeading(): string
    {
        return tr('complaint.report.heading', [], null, 'dashboard') ?: 'تقرير الشكاوي';
    }

    public static function getNavigationLabel(): string
    {
        return tr('complaint.report.navigation', [], null, 'dashboard') ?: 'تقرير الشكاوي';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->from_date = now()->startOfMonth()->format('Y-m-d');
        $this->to_date = now()->format('Y-m-d');
    }

    public function getStats(): array
    {
        $query = Complaint::query();

        if ($this->branch_id) {
            $query->where('branch_id', $this->branch_id);
        }

        if ($this->from_date) {
            $query->whereDate('created_at', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        $resolved = (clone $query)->where('status', 'resolved')->count();
        $inProgress = (clone $query)->where('status', 'in_progress')->count();

        $resolvedDetails = (clone $query)
            ->where('status', 'resolved')
            ->with(['branch', 'assignedUser'])
            ->get();

        $inProgressDetails = (clone $query)
            ->where('status', 'in_progress')
            ->with(['branch', 'assignedUser'])
            ->get();

        return [
            'resolved' => [
                'count' => $resolved,
                'details' => $resolvedDetails,
            ],
            'in_progress' => [
                'count' => $inProgress,
                'details' => $inProgressDetails,
            ],
        ];
    }

    public function getBranches(): array
    {
        return Cache::remember('complaint_report.branches', 21600, function () {
            return Branch::active()->get()->pluck('name', 'id')->toArray();
        });
    }
}
