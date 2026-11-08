<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\BankAccountType;

class BankAccountTypeController extends ApiCrudController
{
    protected $model = BankAccountType::class;

    protected $resource = 'BankAccountType';

    protected $resourcePlural = 'BankAccountTypes';

    protected $searchable = ['bank_account_type_name'];

    protected $rules = [
        'bank_account_type_name' => 'sometimes|required_without:bankAccountTypeName|string|max:255',
        'bankAccountTypeName' => 'sometimes|required_without:bank_account_type_name|string|max:255',
    ];
}
