<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\BankAccount;

class BankAccountController extends ApiCrudController
{
    protected $model = BankAccount::class;

    protected $resource = 'Bank account';

    protected $resourcePlural = 'Bank accounts';

    protected $searchable = ['bank_account_name', 'account_number', 'bank_name'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'bank_account_name' => 'required|string|max:255',
    ];

    protected $updateRules = [];

    protected $listFormat = 'compact';

    protected $useRequestAll = true;
}
