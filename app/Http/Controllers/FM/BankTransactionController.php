<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\BankTransaction;
use Illuminate\Http\Request;

class BankTransactionController extends Controller
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

            $query = BankTransaction::query();

            if ($search) {
                $query->where('bank_transaction_code', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'BankTransactions fetched successfully!',
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
            $item = BankTransaction::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'BankTransaction fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'BankTransaction not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bank_transaction_code' => 'sometimes|required_without:bankTransactionCode|string|max:255',
                'bankTransactionCode' => 'sometimes|required_without:bank_transaction_code|string|max:255',
                'bank_account_id' => 'sometimes|required_without:bankAccountId|integer',
                'bankAccountId' => 'sometimes|required_without:bank_account_id|integer',
                'transaction_type' => 'sometimes|nullable|string|max:255',
                'transactionType' => 'sometimes|nullable|string|max:255',
                'transaction_date' => 'sometimes|nullable|date',
                'transactionDate' => 'sometimes|nullable|date',
                'bank_transaction_status_name' => 'sometimes|nullable|string|max:255',
                'bankTransactionStatusName' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'currency_id' => 'sometimes|nullable|integer',
                'currencyId' => 'sometimes|nullable|integer',
                'reference_number' => 'sometimes|nullable|string|max:255',
                'referenceNumber' => 'sometimes|nullable|string|max:255',
                'total_allocated_amount' => 'sometimes|nullable|numeric',
                'totalAllocatedAmount' => 'sometimes|nullable|numeric',
                'total_un_allocated_amount' => 'sometimes|nullable|numeric',
                'totalUnAllocatedAmount' => 'sometimes|nullable|numeric',
                'party_type' => 'sometimes|nullable|string|max:255',
                'partyType' => 'sometimes|nullable|string|max:255',
                'party_name' => 'sometimes|nullable|string|max:255',
                'partyName' => 'sometimes|nullable|string|max:255',
                'party_account_number' => 'sometimes|nullable|string|max:255',
                'partyAccountNumber' => 'sometimes|nullable|string|max:255',
                'party_iban' => 'sometimes|nullable|string|max:255',
                'partyIban' => 'sometimes|nullable|string|max:255',
            ]);

            $data = [
                'bank_transaction_code' => $request->input('bank_transaction_code') ?? $request->input('bankTransactionCode'),
                'bank_account_id' => $request->input('bank_account_id') ?? $request->input('bankAccountId'),
                'transaction_type' => $request->input('transaction_type') ?? $request->input('transactionType'),
                'transaction_date' => $request->input('transaction_date') ?? $request->input('transactionDate'),
                'bank_transaction_status_name' => $request->input('bank_transaction_status_name') ?? $request->input('bankTransactionStatusName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'currency_id' => $request->input('currency_id') ?? $request->input('currencyId'),
                'reference_number' => $request->input('reference_number') ?? $request->input('referenceNumber'),
                'total_allocated_amount' => $request->input('total_allocated_amount') ?? $request->input('totalAllocatedAmount'),
                'total_un_allocated_amount' => $request->input('total_un_allocated_amount') ?? $request->input('totalUnAllocatedAmount'),
                'party_type' => $request->input('party_type') ?? $request->input('partyType'),
                'party_name' => $request->input('party_name') ?? $request->input('partyName'),
                'party_account_number' => $request->input('party_account_number') ?? $request->input('partyAccountNumber'),
                'party_iban' => $request->input('party_iban') ?? $request->input('partyIban'),
            ];

            $item = BankTransaction::create($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['bank_transaction_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'BankTransaction created successfully!',
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
                'bank_transaction_code' => 'sometimes|required_without:bankTransactionCode|string|max:255',
                'bankTransactionCode' => 'sometimes|required_without:bank_transaction_code|string|max:255',
                'bank_account_id' => 'sometimes|required_without:bankAccountId|integer',
                'bankAccountId' => 'sometimes|required_without:bank_account_id|integer',
                'transaction_type' => 'sometimes|nullable|string|max:255',
                'transactionType' => 'sometimes|nullable|string|max:255',
                'transaction_date' => 'sometimes|nullable|date',
                'transactionDate' => 'sometimes|nullable|date',
                'bank_transaction_status_name' => 'sometimes|nullable|string|max:255',
                'bankTransactionStatusName' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'currency_id' => 'sometimes|nullable|integer',
                'currencyId' => 'sometimes|nullable|integer',
                'reference_number' => 'sometimes|nullable|string|max:255',
                'referenceNumber' => 'sometimes|nullable|string|max:255',
                'total_allocated_amount' => 'sometimes|nullable|numeric',
                'totalAllocatedAmount' => 'sometimes|nullable|numeric',
                'total_un_allocated_amount' => 'sometimes|nullable|numeric',
                'totalUnAllocatedAmount' => 'sometimes|nullable|numeric',
                'party_type' => 'sometimes|nullable|string|max:255',
                'partyType' => 'sometimes|nullable|string|max:255',
                'party_name' => 'sometimes|nullable|string|max:255',
                'partyName' => 'sometimes|nullable|string|max:255',
                'party_account_number' => 'sometimes|nullable|string|max:255',
                'partyAccountNumber' => 'sometimes|nullable|string|max:255',
                'party_iban' => 'sometimes|nullable|string|max:255',
                'partyIban' => 'sometimes|nullable|string|max:255',
            ]);

            $item = BankTransaction::findOrFail($request->id);
            $data = [
                'bank_transaction_code' => $request->input('bank_transaction_code') ?? $request->input('bankTransactionCode'),
                'bank_account_id' => $request->input('bank_account_id') ?? $request->input('bankAccountId'),
                'transaction_type' => $request->input('transaction_type') ?? $request->input('transactionType'),
                'transaction_date' => $request->input('transaction_date') ?? $request->input('transactionDate'),
                'bank_transaction_status_name' => $request->input('bank_transaction_status_name') ?? $request->input('bankTransactionStatusName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'currency_id' => $request->input('currency_id') ?? $request->input('currencyId'),
                'reference_number' => $request->input('reference_number') ?? $request->input('referenceNumber'),
                'total_allocated_amount' => $request->input('total_allocated_amount') ?? $request->input('totalAllocatedAmount'),
                'total_un_allocated_amount' => $request->input('total_un_allocated_amount') ?? $request->input('totalUnAllocatedAmount'),
                'party_type' => $request->input('party_type') ?? $request->input('partyType'),
                'party_name' => $request->input('party_name') ?? $request->input('partyName'),
                'party_account_number' => $request->input('party_account_number') ?? $request->input('partyAccountNumber'),
                'party_iban' => $request->input('party_iban') ?? $request->input('partyIban'),
            ];

            $item->update($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['bank_transaction_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'BankTransaction updated successfully!',
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
            $item = BankTransaction::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankTransaction deleted successfully!',
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
            BankTransaction::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankTransactions deleted successfully!',
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
}
