<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\AssetMovement;

class AssetMovementController extends ApiCrudController
{
    protected $model = AssetMovement::class;

    protected $resource = 'AssetMovement';

    protected $resourcePlural = 'AssetMovements';

    protected $searchable = ['asset_movement_serial_number'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'asset_movement_serial_number' => 'sometimes|required_without:assetMovementSerialNumber|string|max:255',
        'assetMovementSerialNumber' => 'sometimes|required_without:asset_movement_serial_number|string|max:255',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'transaction_date' => 'sometimes|nullable|date',
        'transactionDate' => 'sometimes|nullable|date',
        'purpose_of_movement' => 'sometimes|nullable|string|max:255',
        'purposeOfMovement' => 'sometimes|nullable|string|max:255',
        'asset_id' => 'sometimes|nullable|integer',
        'assetId' => 'sometimes|nullable|integer',
        'asset_location_id' => 'sometimes|nullable|integer',
        'assetLocationId' => 'sometimes|nullable|integer',
    ];
}
