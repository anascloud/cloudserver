<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\AssetMaintenance;

class AssetMaintenanceController extends ApiCrudController
{
    protected $model = AssetMaintenance::class;

    protected $resource = 'AssetMaintenance';

    protected $resourcePlural = 'AssetMaintenances';

    protected $searchable = ['asset_maintenance_serial_number'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'asset_maintenance_serial_number' => 'sometimes|required_without:assetMaintenanceSerialNumber|string|max:255',
        'assetMaintenanceSerialNumber' => 'sometimes|required_without:asset_maintenance_serial_number|string|max:255',
        'asset_id' => 'sometimes|nullable|integer',
        'assetId' => 'sometimes|nullable|integer',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
    ];

    protected $syncDetails = true;
}
