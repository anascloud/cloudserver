<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\BankReconciliation;
use Illuminate\Http\Request;

class BankReconciliationController extends ApiCrudController
{
    protected $model = BankReconciliation::class;

    protected $resource = 'BankReconciliation';

    protected $resourcePlural = 'BankReconciliations';

    protected $searchable = ['id'];

    protected $filters = ['companyId' => 'company_id'];

    protected $useRequestAll = true;

    public function getUnreconciledTransactions(Request $request)
    {
        try {
            $query = \App\Models\FM\BankTransaction::where('status', '!=', 'Reconciled');

            if ($request->filled('bankAccountId')) {
                $query->where('bank_account_id', $request->input('bankAccountId'));
            }

            if ($request->filled('companyId')) {
                $query->where('company_id', $request->input('companyId'));
            }

            if ($request->filled('fromDate')) {
                $query->where('transaction_date', '>=', $request->input('fromDate'));
            }

            if ($request->filled('toDate')) {
                $query->where('transaction_date', '<=', $request->input('toDate'));
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

            if (is_array($entries) && !empty($entries)) {
                $this->insertDetails($transaction, $entries);
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
            $query = \App\Models\FM\Payment::where('payment_status', '!=', 'Reconciled');

            if ($request->filled('companyId')) {
                $query->where('company_id', $request->input('companyId'));
            }

            if ($request->filled('companyBankAccountId')) {
                $query->where('company_bank_account_id', $request->input('companyBankAccountId'));
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
