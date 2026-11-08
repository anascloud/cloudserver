<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\BankClearance;
use Illuminate\Http\Request;

class BankClearanceController extends ApiCrudController
{
    protected $model = BankClearance::class;

    protected $resource = 'BankClearance';

    protected $resourcePlural = 'BankClearances';

    protected $searchable = ['payment_no'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
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
    ];

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
