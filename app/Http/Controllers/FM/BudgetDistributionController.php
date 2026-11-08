<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\BudgetDistribution;

class BudgetDistributionController extends ApiCrudController
{
    protected $model = BudgetDistribution::class;

    protected $resource = 'BudgetDistribution';

    protected $resourcePlural = 'BudgetDistributions';

    protected $searchable = ['budget_distribution_name'];

    protected $rules = [
        'budget_distribution_name' => 'sometimes|required_without:budgetDistributionName|string|max:255',
        'budgetDistributionName' => 'sometimes|required_without:budget_distribution_name|string|max:255',
    ];

    protected $syncDetails = true;
}
