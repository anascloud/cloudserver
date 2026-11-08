<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\BankStatement;

class BankStatementController extends ApiCrudController
{
    protected $model = BankStatement::class;

    protected $resource = 'BankStatement';

    protected $resourcePlural = 'BankStatements';

    protected $searchable = ['id'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'bank_account_id' => 'sometimes|nullable|integer',
        'bankAccountId' => 'sometimes|nullable|integer',
        'currency_id' => 'sometimes|nullable|integer',
        'currencyId' => 'sometimes|nullable|integer',
        'bs_file' => 'sometimes|nullable|file',
        'bsFile' => 'sometimes|nullable|file',
    ];

    protected $fileFields = ['bs_file' => 'bank_statements'];

    protected $syncDetails = true;
}
