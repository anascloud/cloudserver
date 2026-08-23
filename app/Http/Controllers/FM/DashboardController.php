<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\BankAccount;
use App\Models\FM\Company;
use App\Models\FM\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getTopCompanyIncome', 'getCompanyWiseProfit', 'getTopBankAccountsWithHistory', 'getCashFlow']]);
    }

    public function getTopCompanyIncome(Request $request)
    {
        try {
            $totalResult = Payment::where('transaction_type', 'Receive')
                ->selectRaw('COALESCE(SUM(payment_amount), 0) as total_income')
                ->first();

            $topCompanies = Payment::where('transaction_type', 'Receive')
                ->join('fm_companies', 'fm_payments.company_id', '=', 'fm_companies.id')
                ->select('fm_companies.id as companyId', 'fm_companies.company_name as companyName', 'fm_companies.logo_url as logoUrl')
                ->groupBy('fm_companies.id', 'fm_companies.company_name', 'fm_companies.logo_url')
                ->orderByRaw('SUM(fm_payments.payment_amount) DESC')
                ->limit(5)
                ->get();

            $monthlyIncome = Payment::where('transaction_type', 'Receive')
                ->whereYear('posting_date', date('Y'))
                ->selectRaw('MONTH(posting_date) as monthNumber')
                ->selectRaw('CASE MONTH(posting_date) WHEN 1 THEN "January" WHEN 2 THEN "February" WHEN 3 THEN "March" WHEN 4 THEN "April" WHEN 5 THEN "May" WHEN 6 THEN "June" WHEN 7 THEN "July" WHEN 8 THEN "August" WHEN 9 THEN "September" WHEN 10 THEN "October" WHEN 11 THEN "November" WHEN 12 THEN "December" END as monthName')
                ->selectRaw('COALESCE(SUM(payment_amount), 0) as monthlyIncome')
                ->groupByRaw('MONTH(posting_date), monthName')
                ->orderBy('monthNumber')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Top company income report fetched successfully!',
                'errors' => null,
                'data' => [
                    'totalIncome' => ['totalIncome' => (float) $totalResult->total_income],
                    'topCompanies' => $topCompanies,
                    'monthlyIncome' => $monthlyIncome,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'errors' => null, 'data' => null], 500);
        }
    }

    public function getCompanyWiseProfit(Request $request)
    {
        try {
            $companyId = $request->input('companyId');

            $query = Company::query()
                ->select('fm_companies.id as companyId', 'fm_companies.company_name as companyName')
                ->selectRaw('COALESCE(SUM(CASE WHEN fm_payments.transaction_type = "Receive" THEN fm_payments.payment_amount ELSE 0 END), 0) as totalIncome')
                ->selectRaw('COALESCE(SUM(CASE WHEN fm_payments.transaction_type = "Pay" THEN fm_payments.payment_amount ELSE 0 END), 0) as totalExpense')
                ->leftJoin('fm_payments', 'fm_companies.id', '=', 'fm_payments.company_id')
                ->groupBy('fm_companies.id', 'fm_companies.company_name');

            if ($companyId) {
                $query->where('fm_companies.id', $companyId);
            }

            $results = $query->get()->map(function ($item) {
                $profit = $item->totalIncome - $item->totalExpense;
                $percentage = $item->totalIncome > 0 ? round(($profit / $item->totalIncome) * 100, 2) : 0;
                return [
                    'companyId' => $item->companyId,
                    'companyName' => $item->companyName,
                    'totalProfit' => (float) $profit,
                    'profitPercentage' => (float) $percentage,
                ];
            });

            if ($companyId && $results->count() === 1) {
                return response()->json([
                    'status' => true,
                    'message' => 'Company wise profit fetched successfully!',
                    'errors' => null,
                    'data' => $results->first(),
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Company wise profit fetched successfully!',
                'errors' => null,
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'errors' => null, 'data' => null], 500);
        }
    }

    public function getTopBankAccountsWithHistory(Request $request)
    {
        try {
            $periodType = $request->input('periodType', 'monthly');

            $accounts = BankAccount::orderBy('current_balance', 'DESC')
                ->limit(5)
                ->get()
                ->map(function ($account) {
                    return [
                        'bankAccountId' => $account->id,
                        'bankAccountName' => $account->bank_account_name,
                        'bankAccountTypeName' => $account->bank_account_type_name ?? 'N/A',
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => 'Top bank accounts fetched successfully!',
                'errors' => null,
                'data' => $accounts,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'errors' => null, 'data' => null], 500);
        }
    }

    public function getCashFlow(Request $request)
    {
        try {
            $companyId = $request->input('companyId');
            $startDate = $request->input('startDate', date('Y-01-01'));
            $endDate = $request->input('endDate', date('Y-m-d'));

            $query = Payment::whereBetween('posting_date', [$startDate, $endDate]);

            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $income = (clone $query)->where('transaction_type', 'Receive')->sum('payment_amount');
            $expenses = (clone $query)->where('transaction_type', 'Pay')->sum('payment_amount');

            $cashFlow = [
                'operations' => [
                    'id' => 'operations',
                    'name' => 'Cash Flow from Operations',
                    'amount' => (float) ($income - $expenses),
                    'children' => [
                        ['id' => 'op_income', 'name' => 'Income from Customers', 'amount' => (float) $income],
                        ['id' => 'op_expenses', 'name' => 'Payments to Suppliers', 'amount' => (float) -$expenses],
                    ],
                    'total' => ['id' => 'op_total', 'name' => 'Net Cash from Operations', 'amount' => (float) ($income - $expenses)],
                ],
                'investing' => [
                    'id' => 'investing',
                    'name' => 'Cash Flow from Investing',
                    'amount' => 0,
                    'children' => [],
                    'total' => ['id' => 'inv_total', 'name' => 'Net Cash from Investing', 'amount' => 0],
                ],
                'financing' => [
                    'id' => 'financing',
                    'name' => 'Cash Flow from Financing',
                    'amount' => 0,
                    'children' => [],
                    'total' => ['id' => 'fin_total', 'name' => 'Net Cash from Financing', 'amount' => 0],
                ],
                'netChange' => ['id' => 'net_change', 'name' => 'Net Change in Cash', 'amount' => (float) ($income - $expenses)],
            ];

            return response()->json([
                'status' => true,
                'message' => 'Cash flow report fetched successfully!',
                'errors' => null,
                'data' => ['cashFlow' => $cashFlow],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'errors' => null, 'data' => null], 500);
        }
    }
}
