<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\AssetDepreciation;

class AssetDepreciationController extends ApiCrudController
{
    protected $model = AssetDepreciation::class;

    protected $resource = 'AssetDepreciation';

    protected $resourcePlural = 'AssetDepreciations';

    protected $searchable = ['asset_depreciation_serial_number'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'asset_depreciation_serial_number' => 'sometimes|required_without:assetDepreciationSerialNumber|string|max:255',
        'assetDepreciationSerialNumber' => 'sometimes|required_without:asset_depreciation_serial_number|string|max:255',
        'asset_id' => 'sometimes|nullable|integer',
        'assetId' => 'sometimes|nullable|integer',
        'finance_book_id' => 'sometimes|nullable|integer',
        'financeBookId' => 'sometimes|nullable|integer',
        'finance_book_name' => 'sometimes|nullable|string|max:255',
        'financeBookName' => 'sometimes|nullable|string|max:255',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'depreciation_method' => 'sometimes|nullable|string|max:255',
        'depreciationMethod' => 'sometimes|nullable|string|max:255',
        'total_depreciation_period' => 'sometimes|nullable|integer',
        'totalDepreciationPeriod' => 'sometimes|nullable|integer',
        'frequency_of_depreciation' => 'sometimes|nullable|string|max:255',
        'frequencyOfDepreciation' => 'sometimes|nullable|string|max:255',
        'expected_value' => 'sometimes|nullable|numeric',
        'expectedValue' => 'sometimes|nullable|numeric',
        'asset_status' => 'sometimes|nullable|string|max:255',
        'assetStatus' => 'sometimes|nullable|string|max:255',
    ];
}
