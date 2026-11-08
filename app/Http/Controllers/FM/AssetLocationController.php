<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\AssetLocation;

class AssetLocationController extends ApiCrudController
{
    protected $model = AssetLocation::class;

    protected $resource = 'AssetLocation';

    protected $resourcePlural = 'AssetLocations';

    protected $searchable = ['asset_location_name'];

    protected $rules = [
        'asset_location_name' => 'sometimes|required_without:assetLocationName|string|max:255',
        'assetLocationName' => 'sometimes|required_without:asset_location_name|string|max:255',
    ];
}
