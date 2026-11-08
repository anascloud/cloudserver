<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\AssetCategory;

class AssetCategoryController extends ApiCrudController
{
    protected $model = AssetCategory::class;

    protected $resource = 'AssetCategory';

    protected $resourcePlural = 'AssetCategorys';

    protected $searchable = ['asset_category_name'];

    protected $rules = [
        'asset_category_name' => 'sometimes|required_without:assetCategoryName|string|max:255',
        'assetCategoryName' => 'sometimes|required_without:asset_category_name|string|max:255',
        'fixed_asset_account_id' => 'sometimes|nullable|integer',
        'fixedAssetAccountId' => 'sometimes|nullable|integer',
    ];
}
