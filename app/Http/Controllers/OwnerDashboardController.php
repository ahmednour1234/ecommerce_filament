<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\HR\LeaveRequest;
use App\Models\HR\ExcuseRequest;
use App\Models\Complaint;
use App\Models\Rental\RentalContract;
use App\Models\Accounting\JournalEntry;
use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use App\Models\Recruitment\Nationality;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        // ── Stats row 1 ──────────────────────────────────────────────────
        $totalContracts   = RecruitmentContract::count();
        $inProgressContracts = RecruitmentContract::whereNotIn('status', ['received'])->count();
        $pendingLeave     = LeaveRequest::where('status', 'pending')->count();
        $pendingExcuse    = ExcuseRequest::where('status', 'pending')->count();

        // ── Stats row 2 ──────────────────────────────────────────────────
        $activeRentals    = RentalContract::where('status', 'active')->count();
        $pendingJournals  = JournalEntry::where('status', 'pending_approval')->count();
        $openComplaints   = Complaint::whereIn('status', ['pending', 'in_progress'])->count();
        $resolvedComplaints = Complaint::where('status', 'resolved')->count();
        $totalComplaints  = Complaint::count();
        $satisfactionRate = $totalComplaints > 0
            ? round(($resolvedComplaints / $totalComplaints) * 100)
            : 0;

        // ── Today's pending actions (dark summary card) ───────────────────
        $pendingFinance   = BranchTransaction::where('status', 'pending')->count();
        $todayPending     = $pendingLeave + $pendingExcuse + $pendingJournals + $pendingFinance + $openComplaints;

        // ── Monthly contracts chart (last 6 months) ───────────────────────
        $months       = [];
        $monthlyData  = [];
        for ($i = 5; $i >= 0; $i--) {
            $date      = Carbon::now()->subMonths($i);
            $months[]  = $date->translatedFormat('F'); // Arabic month name
            $monthlyData[] = RecruitmentContract::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // ── Top nationalities ────────────────────────────────────────────
        $topNationalities = RecruitmentContract::select('nationality_id', DB::raw('count(*) as total'))
            ->with('nationality:id,name')
            ->whereNotNull('nationality_id')
            ->groupBy('nationality_id')
            ->orderByDesc('total')
            ->limit(4)
            ->get()
            ->map(function ($item) use ($totalContracts) {
                return [
                    'name'    => $item->nationality?->name_ar ?? $item->nationality?->name_en ?? 'غير محدد',
                    'count'   => $item->total,
                    'percent' => $totalContracts > 0 ? round(($item->total / $totalContracts) * 100) : 0,
                ];
            });

        // ── Branch revenue / expense table ───────────────────────────────
        $branches = Branch::where('status', 'active')->get();

        $branchStats = $branches->map(function ($branch) {
            $income  = BranchTransaction::where('branch_id', $branch->id)
                ->where('status', 'approved')
                ->whereHas('financeType', fn ($q) => $q->where('kind', 'income'))
                ->sum('amount');

            $expense = BranchTransaction::where('branch_id', $branch->id)
                ->where('status', 'approved')
                ->whereHas('financeType', fn ($q) => $q->where('kind', 'expense'))
                ->sum('amount');

            $total    = Complaint::where('branch_id', $branch->id)->count();
            $resolved = Complaint::where('branch_id', $branch->id)->where('status', 'resolved')->count();

            $resolveRate = $total > 0 ? round(($resolved / $total) * 100) : 0;

            $rating = match (true) {
                $resolveRate >= 90 => 'ممتاز',
                $resolveRate >= 75 => 'جيد جداً',
                $resolveRate >= 60 => 'جيد',
                default            => 'مقبول',
            };

            return [
                'name'           => $branch->name,
                'income'         => $income,
                'expense'        => $expense,
                'complaints'     => $total,
                'rating'         => $rating,
                'resolve_rate'   => $resolveRate,
                'pending_rate'   => 100 - $resolveRate,
            ];
        });

        // ── Latest 4 recruitment contracts ──────────────────────────────
        $latestContracts = RecruitmentContract::with(['client', 'nationality'])
            ->latest()
            ->limit(4)
            ->get();

        // ── HR pending section ───────────────────────────────────────────
        $pendingLeaveToday  = LeaveRequest::where('status', 'pending')
            ->whereDate('created_at', today())
            ->count();
        $scheduledInterviews = 0; // placeholder — extend when Interview model exists

        // ── Accounting pending section ───────────────────────────────────
        $pendingVouchers = BranchTransaction::where('status', 'pending')->count();

        // ── Daily management KPI ─────────────────────────────────────────
        $approvedToday   = JournalEntry::where('status', 'approved')
            ->whereDate('updated_at', today())
            ->count();
        $activeContracts = RentalContract::where('status', 'active')->count();
        $totalApprovals  = JournalEntry::whereDate('updated_at', today())->count();
        $kpiRate         = $totalApprovals > 0 ? round(($approvedToday / $totalApprovals) * 100) : 87;

        return view('owner-dashboard.index', compact(
            'totalContracts',
            'inProgressContracts',
            'pendingLeave',
            'pendingExcuse',
            'activeRentals',
            'pendingJournals',
            'openComplaints',
            'satisfactionRate',
            'todayPending',
            'pendingFinance',
            'months',
            'monthlyData',
            'topNationalities',
            'branchStats',
            'latestContracts',
            'pendingLeaveToday',
            'scheduledInterviews',
            'pendingVouchers',
            'kpiRate',
            'approvedToday',
            'resolvedComplaints',
            'activeContracts',
        ));
    }
}
