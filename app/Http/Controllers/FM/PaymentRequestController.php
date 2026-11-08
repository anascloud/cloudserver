<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\PaymentRequest;

class PaymentRequestController extends ApiCrudController
{
    protected $model = PaymentRequest::class;

    protected $resource = 'PaymentRequest';

    protected $resourcePlural = 'PaymentRequests';

    protected $searchable = ['payment_request_no'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
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
    ];
}
