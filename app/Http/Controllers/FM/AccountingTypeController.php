<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\AccountingType;

class AccountingTypeController extends ApiCrudController
{
    protected $model = AccountingType::class;

    protected $resource = 'Accounting type';

    protected $resourcePlural = 'Accounting types';

    protected $searchable = ['accounting_type_name'];

    protected $rules = [
        'accounting_type_name' => 'required_without:accountingTypeName|string|max:255',
        'accountingTypeName' => 'required_without:accounting_type_name|string|max:255',
        'parent_id' => 'nullable|integer|exists:accounting_types,id',
    ];
}
