<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\BudgetAgainst;

class BudgetAgainstController extends ApiCrudController
{
    protected $model = BudgetAgainst::class;

    protected $resource = 'BudgetAgainst';

    protected $resourcePlural = 'BudgetAgainsts';

    protected $searchable = ['budget_against_name'];

    protected $rules = [
        'budget_against_name' => 'sometimes|required_without:budgetAgainstName|string|max:255',
        'budgetAgainstName' => 'sometimes|required_without:budget_against_name|string|max:255',
    ];
}
