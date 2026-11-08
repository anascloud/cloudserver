<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\Company;

class CompanyController extends ApiCrudController
{
    protected $model = Company::class;

    protected $resource = 'Company';

    protected $resourcePlural = 'Companies';

    protected $searchable = ['company_name', 'email', 'phone_number', 'domain'];

    protected $rules = [
        'company_name' => 'required|string|max:255',
    ];

    protected $updateRules = [];

    protected $listFormat = 'compact';

    protected $useRequestAll = true;
}
