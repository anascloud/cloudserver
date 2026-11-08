<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\TaxTemplate;

class TaxTemplateController extends ApiCrudController
{
    protected $model = TaxTemplate::class;

    protected $resource = 'TaxTemplate';

    protected $resourcePlural = 'TaxTemplates';

    protected $searchable = ['tax_template_name'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'tax_template_name' => 'sometimes|required_without:taxTemplateName|string|max:255',
        'taxTemplateName' => 'sometimes|required_without:tax_template_name|string|max:255',
        'tax_template_type_id' => 'sometimes|nullable|integer',
        'taxTemplateTypeId' => 'sometimes|nullable|integer',
        'template_type' => 'sometimes|nullable|string|max:255',
        'templateType' => 'sometimes|nullable|string|max:255',
        'tax_category_id' => 'sometimes|nullable|integer',
        'taxCategoryId' => 'sometimes|nullable|integer',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'is_active' => 'sometimes|boolean',
        'isActive' => 'sometimes|boolean',
    ];

    protected $syncDetails = true;
}
