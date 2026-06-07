<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Slip;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the payroll dashboard.
     */
    public function index(): Response
    {
        // Total Outcome for salary (PAID slips)
        $totalOutcome = Slip::where('status', 'PAID')->sum('total_salary');

        // Total Karyawans
        $totalKaryawans = Karyawan::count();

        // Total Outcome Salary Monthly (Grouped by period)
        $monthlyOutcomeRaw = Slip::select(
                'periode_start',
                DB::raw('SUM(total_salary) as total'),
                DB::raw('COUNT(id) as count')
            )
            ->where('status', 'PAID')
            ->groupBy('periode_start')
            ->orderBy('periode_start', 'asc')
            ->get();

        $monthlyOutcome = $monthlyOutcomeRaw->map(function ($item) {
            // Convert YYYY-MM to readable name e.g. June 2026
            $date = \Carbon\Carbon::createFromFormat('Y-m', $item->periode_start);
            return [
                'period' => $item->periode_start,
                'period_name' => $date->format('F Y'),
                'total' => (float) $item->total,
                'count' => $item->count,
            ];
        });

        // Some extra stats for a premium dashboard:
        // Outstanding draft count and sum
        $draftCount = Slip::where('status', 'DRAFT')->count();
        $draftSum = Slip::where('status', 'DRAFT')->sum('total_salary');

        // Detect latest paid slip year or default to current year
        $latestPaidSlip = Slip::where('status', 'PAID')->orderBy('periode_start', 'desc')->first();
        $chartYear = $latestPaidSlip ? \Carbon\Carbon::createFromFormat('Y-m', $latestPaidSlip->periode_start)->year : now()->year;

        // Fetch monthly totals for the detected year
        $monthlyTotals = Slip::select(
                'periode_start',
                DB::raw('SUM(total_salary) as total')
            )
            ->where('status', 'PAID')
            ->where('periode_start', 'like', $chartYear . '-%')
            ->groupBy('periode_start')
            ->get()
            ->pluck('total', 'periode_start');

        $chartData = [];
        $months = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
            '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
        ];

        foreach ($months as $num => $label) {
            $period = "{$chartYear}-{$num}";
            $chartData[] = [
                'month' => $label,
                'total' => isset($monthlyTotals[$period]) ? (float) $monthlyTotals[$period] : 0.0,
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalOutcome' => (float) $totalOutcome,
                'totalKaryawans' => $totalKaryawans,
                'monthlyOutcome' => $monthlyOutcome,
                'draftCount' => $draftCount,
                'draftSum' => (float) $draftSum,
                'chartYear' => $chartYear,
                'chartData' => $chartData,
            ]
        ]);
    }
}
