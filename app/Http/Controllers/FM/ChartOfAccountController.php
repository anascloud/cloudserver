<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\ChartOfAccount;

class ChartOfAccountController extends ApiCrudController
{
    protected $model = ChartOfAccount::class;

    protected $resource = 'ChartOfAccount';

    protected $resourcePlural = 'ChartOfAccounts';

    protected $searchable = ['account_name'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'account_name' => 'sometimes|required_without:accountName|string|max:255',
        'accountName' => 'sometimes|required_without:account_name|string|max:255',
        'account_name_with_abbr' => 'sometimes|nullable|string|max:255',
        'accountNameWithAbbr' => 'sometimes|nullable|string|max:255',
        'account_number' => 'sometimes|nullable|string|max:255',
        'accountNumber' => 'sometimes|nullable|string|max:255',
        'accounting_type_id' => 'sometimes|nullable|integer',
        'accountingTypeId' => 'sometimes|nullable|integer',
        'parent_account_id' => 'sometimes|nullable|integer',
        'parentAccountId' => 'sometimes|nullable|integer',
        'balance_must_be' => 'sometimes|nullable|string|max:255',
        'balanceMustBe' => 'sometimes|nullable|string|max:255',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'currency_id' => 'sometimes|nullable|integer',
        'currencyId' => 'sometimes|nullable|integer',
        'tax_rate' => 'sometimes|nullable|numeric',
        'taxRate' => 'sometimes|nullable|numeric',
        'is_disabled' => 'sometimes|boolean',
        'isDisabled' => 'sometimes|boolean',
    ];
}
