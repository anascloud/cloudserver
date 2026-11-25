<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'leadOverview',
            'salesRevenueOverview',
            'dealPipelineSummary',
            'customerOverview',
            'leadTargetOverview',
            'revenueTargetOverview',
            'threeMonthsLead',
            'leadSourceOverview',
            'profileSaleOverview',
            'todayLeadAnalytics',
            'thisWeekLeadAnalytics',
            'topLeadSourceAnalytics',
            'monthlyLeadAnalytics',
            'industryLeadAnalytics',
        ]]);
    }

    /**
     * Lead Overview
     *
     * Returns lead counts for the current month with growth vs previous month.
     */
    public function leadOverview(Request $request)
    {
        try {
            $now = now();
            $currentYear = $now->year;
            $currentMonth = $now->month;
            $prevYear = $now->copy()->subMonth()->year;
            $prevMonth = $now->copy()->subMonth()->month;

            $leadsThisMonth = DB::table('leads')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();

            $leadsLastMonth = DB::table('leads')
                ->whereYear('created_at', $prevYear)
                ->whereMonth('created_at', $prevMonth)
                ->count();

            $growthPercentage = $leadsLastMonth > 0
                ? (($leadsThisMonth - $leadsLastMonth) / $leadsLastMonth) * 100
                : 0;

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => 1,
                'count' => 1,
                'data' => [
                    'leadsThisMonth' => $leadsThisMonth,
                    'growthPercentage' => round($growthPercentage, 2),
                    'lastMonthRelative' => $leadsLastMonth > 0 ? (($leadsThisMonth - $leadsLastMonth) / $leadsLastMonth) * 100 . '%' : '+100%',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Sales Revenue Overview
     *
     * Returns revenue for the current month with growth vs previous month.
     */
    public function salesRevenueOverview(Request $request)
    {
        try {
            $now = now();
            $currentYear = $now->year;
            $currentMonth = $now->month;
            $prevYear = $now->copy()->subMonth()->year;
            $prevMonth = $now->copy()->subMonth()->month;

            $revenueThisMonth = (float) DB::table('sales_orders')
                ->whereYear('posting_date', $currentYear)
                ->whereMonth('posting_date', $currentMonth)
                ->sum('total');

            $revenueLastMonth = (float) DB::table('sales_orders')
                ->whereYear('posting_date', $prevYear)
                ->whereMonth('posting_date', $prevMonth)
                ->sum('total');

            $growthPercentage = $revenueLastMonth > 0
                ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100
                : 0;

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => 1,
                'count' => 1,
                'data' => [
                    'revenueThisMonth' => $revenueThisMonth,
                    'growthPercentage' => round($growthPercentage, 2),
                    'lastMonthRelative' => $revenueLastMonth > 0 ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100 . '%' : '+100%',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Deal Pipeline Summary
     *
     * Returns the number of open deals currently in the pipeline.
     */
    public function dealPipelineSummary(Request $request)
    {
        try {
            $closedStages = ['Won', 'Lost', 'Closed Won', 'Closed Lost', 'Closed', 'Cancelled'];

            $pipeline = DB::table('opportunities')
                ->where(function ($q) use ($closedStages) {
                    $q->whereNull('stage')
                        ->orWhereNotIn('stage', $closedStages);
                })
                ->count();

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => 1,
                'count' => 1,
                'data' => $pipeline,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Customer Overview
     *
     * Returns customer counts for the current month with growth vs previous month.
     */
    public function customerOverview(Request $request)
    {
        try {
            $now = now();
            $currentYear = $now->year;
            $currentMonth = $now->month;
            $prevYear = $now->copy()->subMonth()->year;
            $prevMonth = $now->copy()->subMonth()->month;

            $customerThisMonth = DB::table('customer')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();

            $customerLastMonth = DB::table('customer')
                ->whereYear('created_at', $prevYear)
                ->whereMonth('created_at', $prevMonth)
                ->count();

            $growthPercentage = $customerLastMonth > 0
                ? (($customerThisMonth - $customerLastMonth) / $customerLastMonth) * 100
                : 0;

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => 1,
                'count' => 1,
                'data' => [
                    'customerThisMonth' => $customerThisMonth,
                    'growthPercentage' => round($growthPercentage, 2),
                    'lastMonthRelative' => $customerLastMonth > 0 ? (($customerThisMonth - $customerLastMonth) / $customerLastMonth) * 100 . '%' : '+100%',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Lead Target Overview
     *
     * Returns the number of leads generated so far this year.
     */
    public function leadTargetOverview(Request $request)
    {
        try {
            $leadsGenerated = DB::table('leads')
                ->whereYear('created_at', now()->year)
                ->count();

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => 1,
                'count' => 1,
                'data' => [
                    'leadsGenerated' => $leadsGenerated,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Revenue Target Overview
     *
     * Returns the revenue target (from targets) and the achieved revenue
     * for the current year.
     */
    public function revenueTargetOverview(Request $request)
    {
        try {
            $target = (float) DB::table('targets')
                ->where('type', 'like', '%Revenue%')
                ->where('status', 'Active')
                ->sum('target_value');

            $achieved = (float) DB::table('sales_orders')
                ->whereYear('posting_date', now()->year)
                ->sum('total');

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => 1,
                'count' => 1,
                'data' => [
                    'target' => $target,
                    'achieved' => $achieved,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Last Three Months Performance Overview
     *
     * Returns leads created, deals created / won and revenue for the
     * previous three months.
     */
    public function threeMonthsLead(Request $request)
    {
        try {
            $result = [];

            for ($i = 2; $i >= 0; $i--) {
                $month = now()->subMonths($i);

                $leadsCreated = DB::table('leads')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                $dealsCreated = DB::table('opportunities')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                $wonStages = ['Won', 'Closed Won'];

                $dealsWon = DB::table('opportunities')
                    ->whereIn('stage', $wonStages)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                $revenueWon = (float) DB::table('opportunities')
                    ->whereIn('stage', $wonStages)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount');

                $closedStages = ['Won', 'Lost', 'Closed Won', 'Closed Lost', 'Closed', 'Cancelled'];

                $openAmount = (float) DB::table('opportunities')
                    ->where(function ($q) use ($closedStages) {
                        $q->whereNull('stage')
                            ->orWhereNotIn('stage', $closedStages);
                    })
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount');

                $result[] = [
                    'month' => $month->format('F'),
                    'year' => (string) $month->year,
                    'leadsCreated' => $leadsCreated,
                    'dealsCreated' => $dealsCreated,
                    'dealsWon' => $dealsWon,
                    'revenueWon' => $revenueWon,
                    'openAmount' => $openAmount,
                ];
            }

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => count($result),
                'count' => count($result),
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Lead Source Overview
     *
     * Returns the percentage of leads grouped by their lead source.
     */
    public function leadSourceOverview(Request $request)
    {
        try {
            $total = DB::table('leads')->count();

            $sources = DB::table('leads')
                ->select('leadSource as source', DB::raw('COUNT(*) as total_count'))
                ->whereNotNull('leadSource')
                ->where('leadSource', '!=', '')
                ->groupBy('leadSource')
                ->orderByDesc('total_count')
                ->get();

            $data = $sources->map(function ($source) use ($total) {
                return [
                    'source' => $source->source,
                    'percentage' => $total > 0 ? round(((int) $source->total_count / $total) * 100, 2) : 0,
                ];
            })->values();

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => $data->count(),
                'count' => $data->count(),
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Profile Sale Overview
     *
     * Returns the sum of opportunity amounts grouped by deal owner.
     */
    public function profileSaleOverview(Request $request)
    {
        try {
            $data = DB::table('opportunities')
                ->select('dealOwner', DB::raw('COALESCE(SUM(amount), 0) as sumOfAmount'))
                ->whereNotNull('dealOwner')
                ->groupBy('dealOwner')
                ->orderByDesc('sumOfAmount')
                ->get()
                ->map(function ($row) {
                    return [
                        'dealOwner' => $row->dealOwner,
                        'sumOfAmount' => (float) $row->sumOfAmount,
                    ];
                })
                ->values();

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => $data->count(),
                'count' => $data->count(),
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Today Lead Analytics
     *
     * Returns the number of leads created today with growth vs yesterday.
     */
    public function todayLeadAnalytics(Request $request)
    {
        try {
            $now = now();

            $leadsToday = DB::table('leads')
                ->whereBetween('created_at', [$now->copy()->startOfDay(), $now->copy()->endOfDay()])
                ->count();

            $leadsYesterday = DB::table('leads')
                ->whereBetween('created_at', [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()])
                ->count();

            $growthPercentage = $leadsYesterday > 0
                ? (($leadsToday - $leadsYesterday) / $leadsYesterday) * 100
                : 0;

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => 1,
                'count' => 1,
                'data' => [
                    'leadsToday' => $leadsToday,
                    'growthPercentage' => round($growthPercentage, 2),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * This Week Lead Analytics
     *
     * Returns the number of leads created this week with growth vs last week.
     */
    public function thisWeekLeadAnalytics(Request $request)
    {
        try {
            $now = now();
            $startOfWeek = $now->copy()->startOfWeek();
            $endOfWeek = $now->copy()->endOfWeek();
            $startOfLastWeek = $now->copy()->subWeek()->startOfWeek();
            $endOfLastWeek = $now->copy()->subWeek()->endOfWeek();

            $leadsThisWeek = DB::table('leads')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count();

            $leadsLastWeek = DB::table('leads')
                ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
                ->count();

            $growthPercentage = $leadsLastWeek > 0
                ? (($leadsThisWeek - $leadsLastWeek) / $leadsLastWeek) * 100
                : 0;

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => 1,
                'count' => 1,
                'data' => [
                    'leadsThisWeek' => $leadsThisWeek,
                    'growthPercentage' => round($growthPercentage, 2),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Top Lead Source Analytics
     *
     * Returns the top ten lead sources by record count.
     */
    public function topLeadSourceAnalytics(Request $request)
    {
        try {
            $data = DB::table('leads')
                ->select('leadSource', DB::raw('COUNT(*) as recordCount'))
                ->whereNotNull('leadSource')
                ->where('leadSource', '!=', '')
                ->groupBy('leadSource')
                ->orderByDesc('recordCount')
                ->limit(10)
                ->get()
                ->map(function ($row) {
                    return [
                        'leadSource' => $row->leadSource,
                        'recordCount' => (int) $row->recordCount,
                    ];
                })
                ->values();

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => $data->count(),
                'count' => $data->count(),
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Monthly Lead Analytics
     *
     * Returns the number of leads created for each of the last twelve months.
     */
    public function monthlyLeadAnalytics(Request $request)
    {
        try {
            $data = [];

            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);

                $recordCount = DB::table('leads')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                $data[] = [
                    'month' => $month->format('M'),
                    'year' => (string) $month->year,
                    'recordCount' => $recordCount,
                ];
            }

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => count($data),
                'count' => count($data),
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Industry Lead Analytics
     *
     * Returns the percentage of leads grouped by their industry.
     */
    public function industryLeadAnalytics(Request $request)
    {
        try {
            $total = DB::table('leads')->count();

            $data = DB::table('leads')
                ->select('industry', DB::raw('COUNT(*) as total_count'))
                ->whereNotNull('industry')
                ->where('industry', '!=', '')
                ->groupBy('industry')
                ->orderByDesc('total_count')
                ->get()
                ->map(function ($row) use ($total) {
                    return [
                        'industry' => $row->industry,
                        'percentage' => $total > 0 ? round(((int) $row->total_count / $total) * 100, 2) : 0,
                    ];
                })
                ->values();

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => $data->count(),
                'count' => $data->count(),
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }
}
