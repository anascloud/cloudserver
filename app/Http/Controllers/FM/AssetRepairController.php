<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\AssetRepair;

class AssetRepairController extends ApiCrudController
{
    protected $model = AssetRepair::class;

    protected $resource = 'AssetRepair';

    protected $resourcePlural = 'AssetRepairs';

    protected $searchable = ['asset_repair_serial_number'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'asset_repair_serial_number' => 'sometimes|required_without:assetRepairSerialNumber|string|max:255',
        'assetRepairSerialNumber' => 'sometimes|required_without:asset_repair_serial_number|string|max:255',
        'asset_id' => 'sometimes|nullable|integer',
        'assetId' => 'sometimes|nullable|integer',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'failure_date' => 'sometimes|nullable|date',
        'failureDate' => 'sometimes|nullable|date',
        'completion_date' => 'sometimes|nullable|date',
        'completionDate' => 'sometimes|nullable|date',
        'repair_date' => 'sometimes|nullable|date',
        'repairDate' => 'sometimes|nullable|date',
        'purchase_invoice_no' => 'sometimes|nullable|string|max:255',
        'purchaseinvoice_no' => 'sometimes|nullable|string|max:255',
        'expense_account_id' => 'sometimes|nullable|integer',
        'expenseAccountId' => 'sometimes|nullable|integer',
        'repair_cost' => 'sometimes|nullable|numeric',
        'repairCost' => 'sometimes|nullable|numeric',
        'repair_description' => 'sometimes|nullable|string|max:255',
        'repairDescription' => 'sometimes|nullable|string|max:255',
        'repair_status' => 'sometimes|nullable|string|max:255',
        'repairStatus' => 'sometimes|nullable|string|max:255',
    ];
}
