<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\BankClearance;
use Illuminate\Http\Request;

class BankClearanceController extends Controller
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

            $query = BankClearance::query();

            if ($search) {
                $query->where('payment_no', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'BankClearances fetched successfully!',
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
            $item = BankClearance::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'BankClearance fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'BankClearance not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_no' => 'sometimes|required_without:paymentNo|string|max:255',
                'paymentNo' => 'sometimes|required_without:payment_no|string|max:255',
                'posting_date' => 'sometimes|nullable|date',
                'postingDate' => 'sometimes|nullable|date',
                'transaction_type' => 'sometimes|nullable|string|max:255',
                'transactionType' => 'sometimes|nullable|string|max:255',
                'party_type' => 'sometimes|nullable|string|max:255',
                'partyType' => 'sometimes|nullable|string|max:255',
                'party_id' => 'sometimes|nullable|integer',
                'partyId' => 'sometimes|nullable|integer',
                'party_name' => 'sometimes|nullable|string|max:255',
                'partyName' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'mode_of_payment_id' => 'sometimes|nullable|integer',
                'modeOfPaymentId' => 'sometimes|nullable|integer',
                'party_bank_account_id' => 'sometimes|nullable|integer',
                'partyBankAccountId' => 'sometimes|nullable|integer',
                'company_bank_account_id' => 'sometimes|nullable|integer',
                'companyBankAccountId' => 'sometimes|nullable|integer',
                'account_paid_from_id' => 'sometimes|nullable|integer',
                'accountPaidFromId' => 'sometimes|nullable|integer',
                'account_paid_to_id' => 'sometimes|nullable|integer',
                'accountPaidToId' => 'sometimes|nullable|integer',
                'from_currency_id' => 'sometimes|nullable|integer',
                'fromCurrencyId' => 'sometimes|nullable|integer',
                'to_currency_id' => 'sometimes|nullable|integer',
                'toCurrencyId' => 'sometimes|nullable|integer',
                'payment_amount' => 'sometimes|nullable|numeric',
                'paymentAmount' => 'sometimes|nullable|numeric',
                'total_allocation_amount' => 'sometimes|nullable|numeric',
                'totalAllocationAmount' => 'sometimes|nullable|numeric',
                'unallocated_amount' => 'sometimes|nullable|numeric',
                'unallocatedAmount' => 'sometimes|nullable|numeric',
                'different_amount' => 'sometimes|nullable|numeric',
                'differentAmount' => 'sometimes|nullable|numeric',
                'total_tax' => 'sometimes|nullable|numeric',
                'totalTax' => 'sometimes|nullable|numeric',
                'reference_number' => 'sometimes|nullable|string|max:255',
                'referenceNumber' => 'sometimes|nullable|string|max:255',
                'reference_date' => 'sometimes|nullable|date',
                'referenceDate' => 'sometimes|nullable|date',
                'payment_status' => 'sometimes|nullable|string|max:255',
                'paymentStatus' => 'sometimes|nullable|string|max:255',
                'bank_clearence_date' => 'sometimes|nullable|date',
                'bankClearenceDate' => 'sometimes|nullable|date',
            ]);

            $data = [
                'payment_no' => $request->input('payment_no') ?? $request->input('paymentNo'),
                'posting_date' => $request->input('posting_date') ?? $request->input('postingDate'),
                'transaction_type' => $request->input('transaction_type') ?? $request->input('transactionType'),
                'party_type' => $request->input('party_type') ?? $request->input('partyType'),
                'party_id' => $request->input('party_id') ?? $request->input('partyId'),
                'party_name' => $request->input('party_name') ?? $request->input('partyName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'mode_of_payment_id' => $request->input('mode_of_payment_id') ?? $request->input('modeOfPaymentId'),
                'party_bank_account_id' => $request->input('party_bank_account_id') ?? $request->input('partyBankAccountId'),
                'company_bank_account_id' => $request->input('company_bank_account_id') ?? $request->input('companyBankAccountId'),
                'account_paid_from_id' => $request->input('account_paid_from_id') ?? $request->input('accountPaidFromId'),
                'account_paid_to_id' => $request->input('account_paid_to_id') ?? $request->input('accountPaidToId'),
                'from_currency_id' => $request->input('from_currency_id') ?? $request->input('fromCurrencyId'),
                'to_currency_id' => $request->input('to_currency_id') ?? $request->input('toCurrencyId'),
                'payment_amount' => $request->input('payment_amount') ?? $request->input('paymentAmount'),
                'total_allocation_amount' => $request->input('total_allocation_amount') ?? $request->input('totalAllocationAmount'),
                'unallocated_amount' => $request->input('unallocated_amount') ?? $request->input('unallocatedAmount'),
                'different_amount' => $request->input('different_amount') ?? $request->input('differentAmount'),
                'total_tax' => $request->input('total_tax') ?? $request->input('totalTax'),
                'reference_number' => $request->input('reference_number') ?? $request->input('referenceNumber'),
                'reference_date' => $request->input('reference_date') ?? $request->input('referenceDate'),
                'payment_status' => $request->input('payment_status') ?? $request->input('paymentStatus'),
                'bank_clearence_date' => $request->input('bank_clearence_date') ?? $request->input('bankClearenceDate'),
            ];

            $item = BankClearance::create($data);

            return response()->json([
                'status' => true,
                'message' => 'BankClearance created successfully!',
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
            $validated = $request->validate([
                'payment_no' => 'sometimes|required_without:paymentNo|string|max:255',
                'paymentNo' => 'sometimes|required_without:payment_no|string|max:255',
                'posting_date' => 'sometimes|nullable|date',
                'postingDate' => 'sometimes|nullable|date',
                'transaction_type' => 'sometimes|nullable|string|max:255',
                'transactionType' => 'sometimes|nullable|string|max:255',
                'party_type' => 'sometimes|nullable|string|max:255',
                'partyType' => 'sometimes|nullable|string|max:255',
                'party_id' => 'sometimes|nullable|integer',
                'partyId' => 'sometimes|nullable|integer',
                'party_name' => 'sometimes|nullable|string|max:255',
                'partyName' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'mode_of_payment_id' => 'sometimes|nullable|integer',
                'modeOfPaymentId' => 'sometimes|nullable|integer',
                'party_bank_account_id' => 'sometimes|nullable|integer',
                'partyBankAccountId' => 'sometimes|nullable|integer',
                'company_bank_account_id' => 'sometimes|nullable|integer',
                'companyBankAccountId' => 'sometimes|nullable|integer',
                'account_paid_from_id' => 'sometimes|nullable|integer',
                'accountPaidFromId' => 'sometimes|nullable|integer',
                'account_paid_to_id' => 'sometimes|nullable|integer',
                'accountPaidToId' => 'sometimes|nullable|integer',
                'from_currency_id' => 'sometimes|nullable|integer',
                'fromCurrencyId' => 'sometimes|nullable|integer',
                'to_currency_id' => 'sometimes|nullable|integer',
                'toCurrencyId' => 'sometimes|nullable|integer',
                'payment_amount' => 'sometimes|nullable|numeric',
                'paymentAmount' => 'sometimes|nullable|numeric',
                'total_allocation_amount' => 'sometimes|nullable|numeric',
                'totalAllocationAmount' => 'sometimes|nullable|numeric',
                'unallocated_amount' => 'sometimes|nullable|numeric',
                'unallocatedAmount' => 'sometimes|nullable|numeric',
                'different_amount' => 'sometimes|nullable|numeric',
                'differentAmount' => 'sometimes|nullable|numeric',
                'total_tax' => 'sometimes|nullable|numeric',
                'totalTax' => 'sometimes|nullable|numeric',
                'reference_number' => 'sometimes|nullable|string|max:255',
                'referenceNumber' => 'sometimes|nullable|string|max:255',
                'reference_date' => 'sometimes|nullable|date',
                'referenceDate' => 'sometimes|nullable|date',
                'payment_status' => 'sometimes|nullable|string|max:255',
                'paymentStatus' => 'sometimes|nullable|string|max:255',
                'bank_clearence_date' => 'sometimes|nullable|date',
                'bankClearenceDate' => 'sometimes|nullable|date',
            ]);

            $item = BankClearance::findOrFail($request->id);
            $data = [
                'payment_no' => $request->input('payment_no') ?? $request->input('paymentNo'),
                'posting_date' => $request->input('posting_date') ?? $request->input('postingDate'),
                'transaction_type' => $request->input('transaction_type') ?? $request->input('transactionType'),
                'party_type' => $request->input('party_type') ?? $request->input('partyType'),
                'party_id' => $request->input('party_id') ?? $request->input('partyId'),
                'party_name' => $request->input('party_name') ?? $request->input('partyName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'mode_of_payment_id' => $request->input('mode_of_payment_id') ?? $request->input('modeOfPaymentId'),
                'party_bank_account_id' => $request->input('party_bank_account_id') ?? $request->input('partyBankAccountId'),
                'company_bank_account_id' => $request->input('company_bank_account_id') ?? $request->input('companyBankAccountId'),
                'account_paid_from_id' => $request->input('account_paid_from_id') ?? $request->input('accountPaidFromId'),
                'account_paid_to_id' => $request->input('account_paid_to_id') ?? $request->input('accountPaidToId'),
                'from_currency_id' => $request->input('from_currency_id') ?? $request->input('fromCurrencyId'),
                'to_currency_id' => $request->input('to_currency_id') ?? $request->input('toCurrencyId'),
                'payment_amount' => $request->input('payment_amount') ?? $request->input('paymentAmount'),
                'total_allocation_amount' => $request->input('total_allocation_amount') ?? $request->input('totalAllocationAmount'),
                'unallocated_amount' => $request->input('unallocated_amount') ?? $request->input('unallocatedAmount'),
                'different_amount' => $request->input('different_amount') ?? $request->input('differentAmount'),
                'total_tax' => $request->input('total_tax') ?? $request->input('totalTax'),
                'reference_number' => $request->input('reference_number') ?? $request->input('referenceNumber'),
                'reference_date' => $request->input('reference_date') ?? $request->input('referenceDate'),
                'payment_status' => $request->input('payment_status') ?? $request->input('paymentStatus'),
                'bank_clearence_date' => $request->input('bank_clearence_date') ?? $request->input('bankClearenceDate'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'BankClearance updated successfully!',
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
            $item = BankClearance::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankClearance deleted successfully!',
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
            BankClearance::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankClearances deleted successfully!',
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

    public function updateClearanceStatus(Request $request)
    {
        try {
            $item = BankClearance::findOrFail($request->id);
            $item->update([
                'payment_status' => $request->input('payment_status') ?? $request->input('paymentStatus'),
                'bank_clearence_date' => $request->input('bank_clearence_date') ?? $request->input('bankClearenceDate'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Bank clearance status updated successfully!',
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

    public function updateClearanceStatusBatch(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            BankClearance::whereIn('id', $ids)->update([
                'payment_status' => $request->input('payment_status') ?? $request->input('paymentStatus'),
                'bank_clearence_date' => $request->input('bank_clearence_date') ?? $request->input('bankClearenceDate'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Bank clearance statuses updated successfully!',
                'errors' => null,
                'data' => null,
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
}
