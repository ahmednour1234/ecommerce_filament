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
use App\Models\Rental\RentalContractRequest;
use App\Models\Accounting\JournalEntry;
use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use App\Models\Recruitment\Nationality;

class OwnerDashboardController extends Controller
{
    private array $arabicMonths = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];

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

        // ── Contracts by section ─────────────────────────────────────────
        $sectionCounts = RecruitmentContract::select('current_section', DB::raw('count(*) as total'))
            ->groupBy('current_section')
            ->pluck('total', 'current_section')
            ->toArray();

        // ── Contracts by status ───────────────────────────────────────────
        $statusCounts = RecruitmentContract::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── Branch comparison chart (الرياض, عرعر, حفر الباطن) ───────────
        $comparisonBranches = Branch::where('status', 'active')
            ->where(function ($q) {
                $q->where('name', 'like', '%الرياض%')
                  ->orWhere('name', 'like', '%عرعر%')
                  ->orWhere('name', 'like', '%حفر الباطن%');
            })
            ->get();

        $branchComparisonMonths = [];
        $branchComparisonData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $branchComparisonMonths[] = $this->arabicMonths[$date->month];
            foreach ($comparisonBranches as $br) {
                $branchComparisonData[$br->name][] = RecruitmentContract::where('branch_id', $br->id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
            }
        }

        // ── Monthly contracts chart (last 6 months) ───────────────────────
        $months       = [];
        $monthlyData  = [];
        for ($i = 5; $i >= 0; $i--) {
            $date      = Carbon::now()->subMonths($i);
            $months[]  = $this->arabicMonths[$date->month];
            $monthlyData[] = RecruitmentContract::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        // ── Rental contracts stats ──────────────────────────────────────────────────────────────────────────────────────
        $rentalStatusCounts = RentalContract::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $rentalTotalContracts = RentalContract::count();

        $rentalRequestCounts = RentalContractRequest::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $rentalTotalRequests = array_sum($rentalRequestCounts) ?: 0;

        // ── Rental monthly trend (last 6 months, reuse $branchComparisonMonths labels) ─
        $rentalMonthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $rentalMonthlyData[] = RentalContract::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // ── Rental branch comparison ────────────────────────────────────────────────────
        $rentalBranchData = [];
        foreach ($comparisonBranches as $br) {
            $rentalBranchData[$br->name] = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $rentalBranchData[$br->name][] = RentalContract::where('branch_id', $br->id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
            }
        }
        // ── Top nationalities ────────────────────────────────────────────
        $topNationalities = RecruitmentContract::select('nationality_id', DB::raw('count(*) as total'))
            ->with('nationality:id,name_ar,name_en')
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

        // ── Monthly income/expense per branch (last 6 months, 3 main branches) ──
        $financeChartMonths = [];
        $financeChartData   = []; // [branchName => ['income'=>[], 'expense'=>[]]]

        $mainBranchNames = ['الرياض', 'عرعر', 'حفر الباطن'];
        $mainBranches    = Branch::where('status', 'active')
            ->where(function ($q) {
                $q->where('name', 'like', '%الرياض%')
                  ->orWhere('name', 'like', '%عرعر%')
                  ->orWhere('name', 'like', '%حفر الباطن%');
            })->get();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $financeChartMonths[] = $this->arabicMonths[$date->month];
            foreach ($mainBranches as $br) {
                $financeChartData[$br->name]['income'][] = (float) BranchTransaction::where('branch_id', $br->id)
                    ->where('status', 'approved')
                    ->whereHas('financeType', fn ($q) => $q->where('kind', 'income'))
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');

                $financeChartData[$br->name]['expense'][] = (float) BranchTransaction::where('branch_id', $br->id)
                    ->where('status', 'approved')
                    ->whereHas('financeType', fn ($q) => $q->where('kind', 'expense'))
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');
            }
        }

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
            'sectionCounts',
            'statusCounts',
            'branchComparisonMonths',
            'branchComparisonData',
            'rentalStatusCounts',
            'rentalTotalContracts',
            'rentalRequestCounts',
            'rentalTotalRequests',
            'rentalMonthlyData',
            'rentalBranchData',
            'financeChartMonths',
            'financeChartData',
        ));
    }

    public function filter(Request $request)
    {
        $branchId = $request->input('branch_id');
        $period   = (int) $request->input('period', 6);
        $from     = $request->input('from');
        $to       = $request->input('to');

        // Date range helper
        $applyDateRange = function ($query) use ($from, $to) {
            if ($from) $query->whereDate('created_at', '>=', $from);
            if ($to)   $query->whereDate('created_at', '<=', $to);
            return $query;
        };

        $applyBranch = function ($query) use ($branchId) {
            if ($branchId) $query->where('branch_id', $branchId);
            return $query;
        };

        // ── Stats row 1 ──────────────────────────────────────────────────
        $totalContracts      = $applyDateRange(RecruitmentContract::query())->count();
        $inProgressContracts = $applyDateRange(RecruitmentContract::whereNotIn('status', ['received']))->count();

        // ── Section & Status counts ───────────────────────────────────────
        $sectionCounts = $applyDateRange(RecruitmentContract::select('current_section', DB::raw('count(*) as total'))
            ->groupBy('current_section'))->pluck('total', 'current_section')->toArray();

        $statusCounts = $applyDateRange(RecruitmentContract::select('status', DB::raw('count(*) as total'))
            ->groupBy('status'))->pluck('total', 'status')->toArray();
        $pendingLeave        = LeaveRequest::where('status', 'pending')->count();
        $pendingExcuse       = ExcuseRequest::where('status', 'pending')->count();

        // ── Stats row 2 ──────────────────────────────────────────────────
        $activeRentals    = RentalContract::where('status', 'active')->count();
        $pendingJournals  = JournalEntry::where('status', 'pending_approval')->count();
        $openComplaints   = $applyBranch(Complaint::whereIn('status', ['pending', 'in_progress']))->count();
        $resolvedComplaints = $applyBranch(Complaint::where('status', 'resolved'))->count();
        $totalComplaints  = $applyBranch(Complaint::query())->count();
        $satisfactionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100) : 0;

        $pendingFinance   = $applyBranch(BranchTransaction::where('status', 'pending'))->count();
        $todayPending     = $pendingLeave + $pendingExcuse + $pendingJournals + $pendingFinance + $openComplaints;

        $pendingVouchers  = $pendingFinance;

        // ── Monthly chart ─────────────────────────────────────────────────
        $months      = [];
        $monthlyData = [];
        $numMonths   = max(1, min(12, $period));

        for ($i = $numMonths - 1; $i >= 0; $i--) {
            $date        = Carbon::now()->subMonths($i);
            $months[]    = $this->arabicMonths[$date->month];
            $q           = RecruitmentContract::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            $monthlyData[] = $q->count();
        }

        // ── KPI ───────────────────────────────────────────────────────────
        $approvedToday  = JournalEntry::where('status', 'approved')->whereDate('updated_at', today())->count();
        $totalApprovals = JournalEntry::whereDate('updated_at', today())->count();
        $kpiRate        = $totalApprovals > 0 ? round(($approvedToday / $totalApprovals) * 100) : 87;
        $activeContracts = RentalContract::where('status', 'active')->count();

        return response()->json([
            'totalContracts'     => $totalContracts,
            'inProgressContracts'=> $inProgressContracts,
            'pendingLeave'       => $pendingLeave,
            'pendingExcuse'      => $pendingExcuse,
            'activeRentals'      => $activeRentals,
            'pendingJournals'    => $pendingJournals,
            'openComplaints'     => $openComplaints,
            'satisfactionRate'   => $satisfactionRate,
            'todayPending'       => $todayPending,
            'pendingVouchers'    => $pendingVouchers,
            'resolvedComplaints' => $resolvedComplaints,
            'activeContracts'    => $activeContracts,
            'approvedToday'      => $approvedToday,
            'kpiRate'            => $kpiRate,
            'months'             => $months,
            'monthlyData'        => $monthlyData,
            'sectionCounts'      => $sectionCounts,
            'statusCounts'       => $statusCounts,
        ]);
    }
}
