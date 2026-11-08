<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\Budget;

class BudgetController extends ApiCrudController
{
    protected $model = Budget::class;

    protected $resource = 'Budget';

    protected $resourcePlural = 'Budgets';

    protected $searchable = ['budget_name'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'budget_name' => 'sometimes|required_without:budgetName|string|max:255',
        'budgetName' => 'sometimes|required_without:budget_name|string|max:255',
        'budget_against_id' => 'sometimes|nullable|integer',
        'budgetAgainstId' => 'sometimes|nullable|integer',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'fiscal_year_id' => 'sometimes|nullable|integer',
        'fiscalYearId' => 'sometimes|nullable|integer',
        'budget_distribution_id' => 'sometimes|nullable|integer',
        'budgetDistributionId' => 'sometimes|nullable|integer',
        'cost_center_id' => 'sometimes|nullable|integer',
        'costCenterId' => 'sometimes|nullable|integer',
    ];

    protected $syncDetails = true;
}
