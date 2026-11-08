<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\Asset;

class AssetController extends ApiCrudController
{
    protected $model = Asset::class;

    protected $resource = 'Asset';

    protected $resourcePlural = 'Assets';

    protected $searchable = ['asset_name'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'asset_name' => 'sometimes|required_without:assetName|string|max:255',
        'assetName' => 'sometimes|required_without:asset_name|string|max:255',
        'asset_serial_number' => 'sometimes|nullable|string|max:255',
        'assetSerialNumber' => 'sometimes|nullable|string|max:255',
        'product_id' => 'sometimes|nullable|integer',
        'productId' => 'sometimes|nullable|integer',
        'product_name' => 'sometimes|nullable|string|max:255',
        'productName' => 'sometimes|nullable|string|max:255',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'asset_category_id' => 'sometimes|nullable|integer',
        'assetCategoryId' => 'sometimes|nullable|integer',
        'asset_location_id' => 'sometimes|nullable|integer',
        'assetLocationId' => 'sometimes|nullable|integer',
        'asset_owner_name' => 'sometimes|nullable|string|max:255',
        'assetOwnerName' => 'sometimes|nullable|string|max:255',
    ];
}
