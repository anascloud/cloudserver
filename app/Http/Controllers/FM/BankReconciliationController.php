<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\BankReconciliation;
use Illuminate\Http\Request;

class BankReconciliationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
    }

    public function indexAll(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize', $request->input('per_page', 10));
            $search = $request->input('search', '');

            $query = BankReconciliation::query();

            if ($search) {
                $query->where('id', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'BankReconciliations fetched successfully!',
                'errors' => null,
                'data' => [
                    'current_page' => $items->currentPage(),
                    'data' => $items->items(),
                    'first_page_url' => $items->url(1),
                    'from' => $items->firstItem(),
                    'last_page' => $items->lastPage(),
                    'last_page_url' => $items->url($items->lastPage()),
                    'next_page_url' => $items->nextPageUrl(),
                    'path' => $request->url(),
                    'per_page' => $items->perPage(),
                    'prev_page_url' => $items->previousPageUrl(),
                    'to' => $items->lastItem(),
                    'total' => $items->total(),
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

    public function index(Request $request)
    {
        return $this->indexAll($request);
    }

    public function show($id)
    {
        try {
            $item = BankReconciliation::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'BankReconciliation fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'BankReconciliation not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $item = BankReconciliation::create($request->all());


            return response()->json([
                'status' => true,
                'message' => 'BankReconciliation created successfully!',
                'errors' => null,
                'data' => $item,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 422);
        }
    }

    public function update(Request $request)
    {
        try {
            $item = BankReconciliation::findOrFail($request->id);
            $item->update($request->all());


            return response()->json([
                'status' => true,
                'message' => 'BankReconciliation updated successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $item = BankReconciliation::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankReconciliation deleted successfully!',
                'errors' => null,
                'data' => null,
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

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->all();
            BankReconciliation::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankReconciliations deleted successfully!',
                'errors' => null,
                'data' => null,
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

    public function getUnreconciledTransactions(Request $request)
    {
        try {
            $bankAccountId = $request->input('bankAccountId');
            $companyId = $request->input('companyId');
            $fromDate = $request->input('fromDate');
            $toDate = $request->input('toDate');

            $query = \App\Models\FM\BankTransaction::where('status', '!=', 'Reconciled');

            if ($bankAccountId) {
                $query->where('bank_account_id', $bankAccountId);
            }
            if ($companyId) {
                $query->where('company_id', $companyId);
            }
            if ($fromDate) {
                $query->where('transaction_date', '>=', $fromDate);
            }
            if ($toDate) {
                $query->where('transaction_date', '<=', $toDate);
            }

            $transactions = $query->orderBy('transaction_date', 'DESC')->get();

            $closingBalance = $transactions->sum('deposit') - $transactions->sum('withdraw');

            return response()->json([
                'status' => true,
                'message' => 'Unreconciled transactions fetched successfully!',
                'errors' => null,
                'data' => [
                    'closing_balance_as_per_erp' => $closingBalance,
                    'transactions' => $transactions,
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

    public function updateUnreconciledTransaction(Request $request, $id)
    {
        try {
            $transaction = \App\Models\FM\BankTransaction::findOrFail($id);

            $entries = $request->input('entries', []);
            foreach ($entries as $entry) {
                $transaction->details()->create($entry);
            }

            $transaction->update(['status' => 'Reconciled']);

            return response()->json([
                'status' => true,
                'message' => 'Transaction reconciled successfully!',
                'errors' => null,
                'data' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 422);
        }
    }

    public function getUnreconciledPayments(Request $request)
    {
        try {
            $companyId = $request->input('companyId');
            $bankAccountId = $request->input('companyBankAccountId');

            $query = \App\Models\FM\Payment::where('payment_status', '!=', 'Reconciled');

            if ($companyId) {
                $query->where('company_id', $companyId);
            }
            if ($bankAccountId) {
                $query->where('company_bank_account_id', $bankAccountId);
            }

            $payments = $query->orderBy('posting_date', 'DESC')->get();

            return response()->json([
                'status' => true,
                'message' => 'Unreconciled payments fetched successfully!',
                'errors' => null,
                'data' => $payments,
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
