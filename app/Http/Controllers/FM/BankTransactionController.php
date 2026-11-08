<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\BankTransaction;

class BankTransactionController extends ApiCrudController
{
    protected $model = BankTransaction::class;

    protected $resource = 'BankTransaction';

    protected $resourcePlural = 'BankTransactions';

    protected $searchable = ['bank_transaction_code'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
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
    ];

    protected $syncDetails = true;
}
