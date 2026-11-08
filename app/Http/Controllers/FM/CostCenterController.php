<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\CostCenter;

class CostCenterController extends ApiCrudController
{
    protected $model = CostCenter::class;

    protected $resource = 'CostCenter';

    protected $resourcePlural = 'CostCenters';

    protected $searchable = ['cost_center_name'];

    protected $rules = [
        'cost_center_name' => 'sometimes|required_without:costCenterName|string|max:255',
        'costCenterName' => 'sometimes|required_without:cost_center_name|string|max:255',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'is_active' => 'sometimes|boolean',
        'isActive' => 'sometimes|boolean',
    ];
}
