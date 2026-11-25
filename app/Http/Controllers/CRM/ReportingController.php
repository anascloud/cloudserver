<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Salesorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['monthlySalesReport', 'totalSummaryReport', 'dashboardTopUserReport']]);
    }

    /**
     * Monthly Sales Report
     *
     * Returns a paginated list of monthly sales aggregates derived
     * from the sales_orders table.
     */
    public function monthlySalesReport(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $pageIndex = (int) $request->input('pageIndex', $request->input('page', 1));
            $pageSize = (int) $request->input('pageSize', 10);
            $startDate = $request->input('start_date', '');
            $endDate = $request->input('end_date', '');

            $query = DB::table('sales_orders')
                ->leftJoin('sales_order_details', 'sales_orders.id', '=', 'sales_order_details.salesOrderId')
                ->leftJoin('products', 'sales_order_details.productId', '=', 'products.id')
                ->whereNotNull('sales_orders.posting_date')
                ->select(
                    DB::raw('YEAR(sales_orders.posting_date) as sales_year'),
                    DB::raw('MONTH(sales_orders.posting_date) as sales_month'),
                    DB::raw('CASE MONTH(sales_orders.posting_date)
                        WHEN 1 THEN "January"
                        WHEN 2 THEN "February"
                        WHEN 3 THEN "March"
                        WHEN 4 THEN "April"
                        WHEN 5 THEN "May"
                        WHEN 6 THEN "June"
                        WHEN 7 THEN "July"
                        WHEN 8 THEN "August"
                        WHEN 9 THEN "September"
                        WHEN 10 THEN "October"
                        WHEN 11 THEN "November"
                        WHEN 12 THEN "December"
                    END as sales_month_name'),
                    DB::raw('COUNT(DISTINCT sales_orders.id) as number_of_sales'),
                    DB::raw('COALESCE(SUM(sales_order_details.quantity), 0) as volume'),
                    DB::raw('COALESCE(SUM(sales_orders.total), 0) as sale_value'),
                    DB::raw('COALESCE(SUM(sales_orders.discount), 0) as discount'),
                    DB::raw('COALESCE(SUM(sales_orders.tax), 0) as tax'),
                    DB::raw('COALESCE(SUM(sales_order_details.quantity * products.unitCost), 0) as cost')
                )
                ->groupByRaw(
                    'YEAR(sales_orders.posting_date), MONTH(sales_orders.posting_date), CASE MONTH(sales_orders.posting_date) WHEN 1 THEN "January" WHEN 2 THEN "February" WHEN 3 THEN "March" WHEN 4 THEN "April" WHEN 5 THEN "May" WHEN 6 THEN "June" WHEN 7 THEN "July" WHEN 8 THEN "August" WHEN 9 THEN "September" WHEN 10 THEN "October" WHEN 11 THEN "November" WHEN 12 THEN "December" END'
                );

            if (!empty($startDate)) {
                $query->whereDate('sales_orders.posting_date', '>=', $startDate);
            }

            if (!empty($endDate)) {
                $query->whereDate('sales_orders.posting_date', '<=', $endDate);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('sales_orders.salesOrderNo', 'like', "%{$search}%")
                        ->orWhere('sales_orders.title', 'like', "%{$search}%")
                        ->orWhere('sales_orders.stages', 'like', "%{$search}%")
                        ->orWhere('sales_orders.type', 'like', "%{$search}%");
                });
            }

            $rows = $query->get()->groupBy(function ($report) {
                return $report->sales_year . '-' . $report->sales_month;
            });

            $count = $rows->count();

            $monthly = $rows->map(function ($group) {
                $first = $group->first();
                $numberOfSales = $group->sum(function ($item) {
                    return (int) $item->number_of_sales;
                });
                $volume = $group->sum(function ($item) {
                    return (float) $item->volume;
                });
                $saleValue = $group->sum(function ($item) {
                    return (float) $item->sale_value;
                });
                $discount = $group->sum(function ($item) {
                    return (float) $item->discount;
                });
                $tax = $group->sum(function ($item) {
                    return (float) $item->tax;
                });
                $cost = $group->sum(function ($item) {
                    return (float) $item->cost;
                });
                $profit = $saleValue - $cost;

                return [
                    'id' => $first->sales_year . '-' . $first->sales_month,
                    'salesMonthName' => $first->sales_month_name,
                    'salesYear' => (string) $first->sales_year,
                    'numberOfSales' => $numberOfSales,
                    'volume' => round($volume, 2),
                    'saleValue' => round($saleValue, 2),
                    'cost' => round($cost, 2),
                    'discount' => round($discount, 2),
                    'tax' => round($tax, 2),
                    'profit' => round($profit, 2),
                    'revenue' => round($saleValue, 2),
                ];
            })->sortByDesc(function ($item) {
                return $item['id'];
            })->values();

            $data = $monthly
                ->skip(($pageIndex - 1) * $pageSize)
                ->take($pageSize)
                ->values();

            return response()->json([
                'pageIndex' => $pageIndex,
                'pageSize' => $pageSize,
                'count' => $count,
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
     * Total Summary Report
     *
     * Returns aggregate counts for sales, tickets, customers, leads and
     * campaigns along with this month / previous month comparisons.
     */
    public function totalSummaryReport(Request $request)
    {
        try {
            $now = now();
            $currentYear = $now->year;
            $currentMonth = $now->month;
            $previousMonthYear = $now->copy()->subMonth()->year;
            $previousMonth = $now->copy()->subMonth()->month;

            $salesInTable = DB::table('sales_orders')->count();
            $salesThisMonth = DB::table('sales_orders')
                ->whereYear('posting_date', $currentYear)
                ->whereMonth('posting_date', $currentMonth)
                ->count();
            $salesPreviousMonth = DB::table('sales_orders')
                ->whereYear('posting_date', $previousMonthYear)
                ->whereMonth('posting_date', $previousMonth)
                ->count();

            $ticketsInTable = DB::table('tickets')->count();
            $ticketsThisMonth = DB::table('tickets')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();
            $ticketsPreviousMonth = DB::table('tickets')
                ->whereYear('created_at', $previousMonthYear)
                ->whereMonth('created_at', $previousMonth)
                ->count();

            $customersInTable = DB::table('customer')->count();
            $customersThisMonth = DB::table('customer')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();
            $customersPreviousMonth = DB::table('customer')
                ->whereYear('created_at', $previousMonthYear)
                ->whereMonth('created_at', $previousMonth)
                ->count();

            $leadsInTable = DB::table('leads')->count();
            $leadsThisMonth = DB::table('leads')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();
            $leadsPreviousMonth = DB::table('leads')
                ->whereYear('created_at', $previousMonthYear)
                ->whereMonth('created_at', $previousMonth)
                ->count();

            $campaignsInTable = DB::table('campaigns')->count();
            $campaignsThisMonth = DB::table('campaigns')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();
            $campaignsPreviousMonth = DB::table('campaigns')
                ->whereYear('created_at', $previousMonthYear)
                ->whereMonth('created_at', $previousMonth)
                ->count();

            $data = [
                'totalSalesInTable' => $salesInTable,
                'totalSalesThisMonth' => $salesThisMonth,
                'totalSalesPreviousMonth' => $salesPreviousMonth,
                'totalTicketsInTable' => $ticketsInTable,
                'totalTicketsThisMonth' => $ticketsThisMonth,
                'totalTicketsPreviousMonth' => $ticketsPreviousMonth,
                'totalCustomersInTable' => $customersInTable,
                'totalCustomersThisMonth' => $customersThisMonth,
                'totalCustomersPreviousMonth' => $customersPreviousMonth,
                'totalLeadsInTable' => $leadsInTable,
                'totalLeadsThisMonth' => $leadsThisMonth,
                'totalLeadsPreviousMonth' => $leadsPreviousMonth,
                'totalCampaignsInTable' => $campaignsInTable,
                'totalCampaignsThisMonth' => $campaignsThisMonth,
                'totalCampaignsPreviousMonth' => $campaignsPreviousMonth,
            ];

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => count($data),
                'count' => 1,
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
     * Dashboard Top User Report
     *
     * Returns lead activity counts per owner (assignedTo / owner) for
     * today, yesterday, this month, previous month, this year and
     * previous year.
     */
    public function dashboardTopUserReport(Request $request)
    {
        try {
            $now = now();

            $leads = DB::table('leads')
                ->select('assignedTo', 'owner', 'created_at')
                ->get();

            $report = $leads
                ->groupBy(function ($lead) {
                    $owner = trim((string) ($lead->assignedTo ?? $lead->owner ?? ''));
                    return $owner === '' ? '_unassigned' : $owner;
                })
                ->sortByDesc(function ($group) use ($now) {
                    return $group->where('created_at', '>=', $now->copy()->startOfMonth())->count();
                })
                ->take(3)
                ->map(function ($group, $owner) use ($now) {
                    return [
                        'userId' => is_numeric($owner) ? (int) $owner : $owner,
                        'countToday' => $group->where('created_at', '>=', $now->copy()->startOfDay())->count(),
                        'countYesterday' => $group->whereBetween('created_at', [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()])->count(),
                        'countThisMonth' => $group->where('created_at', '>=', $now->copy()->startOfMonth())->count(),
                        'countPreviousMonth' => $group->whereBetween('created_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()])->count(),
                        'countThisYear' => $group->where('created_at', '>=', $now->copy()->startOfYear())->count(),
                        'countPreviousYear' => $group->whereBetween('created_at', [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()])->count(),
                    ];
                })
                ->values();

            return response()->json([
                'pageIndex' => 1,
                'pageSize' => $report->count(),
                'count' => $report->count(),
                'data' => [
                    'leadReports' => $report,
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
}
