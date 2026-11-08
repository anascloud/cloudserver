<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\ZatcaCategory;

class ZatcaCategoryController extends ApiCrudController
{
    protected $model = ZatcaCategory::class;

    protected $resource = 'ZatcaCategory';

    protected $resourcePlural = 'ZatcaCategorys';

    protected $searchable = ['zatca_category_name'];

    protected $rules = [
        'zatca_category_name' => 'sometimes|required_without:zatcaCategoryName|string|max:255',
        'zatcaCategoryName' => 'sometimes|required_without:zatca_category_name|string|max:255',
    ];
}
