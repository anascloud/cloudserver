<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\PaymentRequest;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
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

            $query = PaymentRequest::query();

            if ($search) {
                $query->where('payment_request_no', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'PaymentRequests fetched successfully!',
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
            $item = PaymentRequest::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'PaymentRequest fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'PaymentRequest not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_request_no' => 'sometimes|required_without:paymentRequestNo|string|max:255',
                'paymentRequestNo' => 'sometimes|required_without:payment_request_no|string|max:255',
                'payment_request_type' => 'sometimes|nullable|string|max:255',
                'paymentRequestType' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'transaction_date' => 'sometimes|nullable|date',
                'transactionDate' => 'sometimes|nullable|date',
                'mode_of_payment_id' => 'sometimes|nullable|integer',
                'modeOfPaymentId' => 'sometimes|nullable|integer',
                'party_type' => 'sometimes|nullable|string|max:255',
                'partyType' => 'sometimes|nullable|string|max:255',
                'party_id' => 'sometimes|nullable|integer',
                'partyId' => 'sometimes|nullable|integer',
                'party_name' => 'sometimes|nullable|string|max:255',
                'partyName' => 'sometimes|nullable|string|max:255',
                'reference_type' => 'sometimes|nullable|string|max:255',
                'referenceType' => 'sometimes|nullable|string|max:255',
                'reference_number' => 'sometimes|nullable|string|max:255',
                'referenceNumber' => 'sometimes|nullable|string|max:255',
                'outstanding_amount' => 'sometimes|nullable|numeric',
                'outstandingAmount' => 'sometimes|nullable|numeric',
                'party_account_currency_id' => 'sometimes|nullable|integer',
                'partyAccountCurrencyId' => 'sometimes|nullable|integer',
                'transaction_currency_id' => 'sometimes|nullable|integer',
                'transactionCurrencyId' => 'sometimes|nullable|integer',
                'bank_account_id' => 'sometimes|nullable|integer',
                'bankAccountId' => 'sometimes|nullable|integer',
                'bank_name' => 'sometimes|nullable|string|max:255',
                'bankName' => 'sometimes|nullable|string|max:255',
                'bank_account_number' => 'sometimes|nullable|string|max:255',
                'bankAccountNumber' => 'sometimes|nullable|string|max:255',
            ]);

            $data = [
                'payment_request_no' => $request->input('payment_request_no') ?? $request->input('paymentRequestNo'),
                'payment_request_type' => $request->input('payment_request_type') ?? $request->input('paymentRequestType'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'transaction_date' => $request->input('transaction_date') ?? $request->input('transactionDate'),
                'mode_of_payment_id' => $request->input('mode_of_payment_id') ?? $request->input('modeOfPaymentId'),
                'party_type' => $request->input('party_type') ?? $request->input('partyType'),
                'party_id' => $request->input('party_id') ?? $request->input('partyId'),
                'party_name' => $request->input('party_name') ?? $request->input('partyName'),
                'reference_type' => $request->input('reference_type') ?? $request->input('referenceType'),
                'reference_number' => $request->input('reference_number') ?? $request->input('referenceNumber'),
                'outstanding_amount' => $request->input('outstanding_amount') ?? $request->input('outstandingAmount'),
                'party_account_currency_id' => $request->input('party_account_currency_id') ?? $request->input('partyAccountCurrencyId'),
                'transaction_currency_id' => $request->input('transaction_currency_id') ?? $request->input('transactionCurrencyId'),
                'bank_account_id' => $request->input('bank_account_id') ?? $request->input('bankAccountId'),
                'bank_name' => $request->input('bank_name') ?? $request->input('bankName'),
                'bank_account_number' => $request->input('bank_account_number') ?? $request->input('bankAccountNumber'),
            ];

            $item = PaymentRequest::create($data);

            return response()->json([
                'status' => true,
                'message' => 'PaymentRequest created successfully!',
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
                'payment_request_no' => 'sometimes|required_without:paymentRequestNo|string|max:255',
                'paymentRequestNo' => 'sometimes|required_without:payment_request_no|string|max:255',
                'payment_request_type' => 'sometimes|nullable|string|max:255',
                'paymentRequestType' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'transaction_date' => 'sometimes|nullable|date',
                'transactionDate' => 'sometimes|nullable|date',
                'mode_of_payment_id' => 'sometimes|nullable|integer',
                'modeOfPaymentId' => 'sometimes|nullable|integer',
                'party_type' => 'sometimes|nullable|string|max:255',
                'partyType' => 'sometimes|nullable|string|max:255',
                'party_id' => 'sometimes|nullable|integer',
                'partyId' => 'sometimes|nullable|integer',
                'party_name' => 'sometimes|nullable|string|max:255',
                'partyName' => 'sometimes|nullable|string|max:255',
                'reference_type' => 'sometimes|nullable|string|max:255',
                'referenceType' => 'sometimes|nullable|string|max:255',
                'reference_number' => 'sometimes|nullable|string|max:255',
                'referenceNumber' => 'sometimes|nullable|string|max:255',
                'outstanding_amount' => 'sometimes|nullable|numeric',
                'outstandingAmount' => 'sometimes|nullable|numeric',
                'party_account_currency_id' => 'sometimes|nullable|integer',
                'partyAccountCurrencyId' => 'sometimes|nullable|integer',
                'transaction_currency_id' => 'sometimes|nullable|integer',
                'transactionCurrencyId' => 'sometimes|nullable|integer',
                'bank_account_id' => 'sometimes|nullable|integer',
                'bankAccountId' => 'sometimes|nullable|integer',
                'bank_name' => 'sometimes|nullable|string|max:255',
                'bankName' => 'sometimes|nullable|string|max:255',
                'bank_account_number' => 'sometimes|nullable|string|max:255',
                'bankAccountNumber' => 'sometimes|nullable|string|max:255',
            ]);

            $item = PaymentRequest::findOrFail($request->id);
            $data = [
                'payment_request_no' => $request->input('payment_request_no') ?? $request->input('paymentRequestNo'),
                'payment_request_type' => $request->input('payment_request_type') ?? $request->input('paymentRequestType'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'transaction_date' => $request->input('transaction_date') ?? $request->input('transactionDate'),
                'mode_of_payment_id' => $request->input('mode_of_payment_id') ?? $request->input('modeOfPaymentId'),
                'party_type' => $request->input('party_type') ?? $request->input('partyType'),
                'party_id' => $request->input('party_id') ?? $request->input('partyId'),
                'party_name' => $request->input('party_name') ?? $request->input('partyName'),
                'reference_type' => $request->input('reference_type') ?? $request->input('referenceType'),
                'reference_number' => $request->input('reference_number') ?? $request->input('referenceNumber'),
                'outstanding_amount' => $request->input('outstanding_amount') ?? $request->input('outstandingAmount'),
                'party_account_currency_id' => $request->input('party_account_currency_id') ?? $request->input('partyAccountCurrencyId'),
                'transaction_currency_id' => $request->input('transaction_currency_id') ?? $request->input('transactionCurrencyId'),
                'bank_account_id' => $request->input('bank_account_id') ?? $request->input('bankAccountId'),
                'bank_name' => $request->input('bank_name') ?? $request->input('bankName'),
                'bank_account_number' => $request->input('bank_account_number') ?? $request->input('bankAccountNumber'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'PaymentRequest updated successfully!',
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
            $item = PaymentRequest::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'PaymentRequest deleted successfully!',
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
            PaymentRequest::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'PaymentRequests deleted successfully!',
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
