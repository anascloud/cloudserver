<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\TaxCategory;

class TaxCategoryController extends ApiCrudController
{
    protected $model = TaxCategory::class;

    protected $resource = 'TaxCategory';

    protected $resourcePlural = 'TaxCategorys';

    protected $searchable = ['tax_category_name'];

    protected $rules = [
        'tax_category_name' => 'sometimes|required_without:taxCategoryName|string|max:255',
        'taxCategoryName' => 'sometimes|required_without:tax_category_name|string|max:255',
        'zatca_category_id' => 'sometimes|nullable|integer',
        'zatcaCategoryId' => 'sometimes|nullable|integer',
        'is_active' => 'sometimes|boolean',
        'isActive' => 'sometimes|boolean',
    ];
}
