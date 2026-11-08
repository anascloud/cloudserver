<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\TaxRule;

class TaxRuleController extends ApiCrudController
{
    protected $model = TaxRule::class;

    protected $resource = 'TaxRule';

    protected $resourcePlural = 'TaxRules';

    protected $searchable = ['serial_number'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'serial_number' => 'sometimes|required_without:serialNumber|string|max:255',
        'serialNumber' => 'sometimes|required_without:serial_number|string|max:255',
        'rule_type' => 'sometimes|nullable|string|max:255',
        'ruleType' => 'sometimes|nullable|string|max:255',
        'tax_template_id' => 'sometimes|nullable|integer',
        'taxTemplateId' => 'sometimes|nullable|integer',
        'customer_id' => 'sometimes|nullable|integer',
        'customerId' => 'sometimes|nullable|integer',
        'customer_name' => 'sometimes|nullable|string|max:255',
        'customerName' => 'sometimes|nullable|string|max:255',
        'supplier_id' => 'sometimes|nullable|integer',
        'supplierId' => 'sometimes|nullable|integer',
        'supplier_name' => 'sometimes|nullable|string|max:255',
        'supplierName' => 'sometimes|nullable|string|max:255',
        'product_id' => 'sometimes|nullable|integer',
        'productId' => 'sometimes|nullable|integer',
        'product_name' => 'sometimes|nullable|string|max:255',
        'productName' => 'sometimes|nullable|string|max:255',
        'tax_category_id' => 'sometimes|nullable|integer',
        'taxCategoryId' => 'sometimes|nullable|integer',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'valid_from' => 'sometimes|nullable|date',
        'validFrom' => 'sometimes|nullable|date',
        'valid_to' => 'sometimes|nullable|date',
        'validTo' => 'sometimes|nullable|date',
    ];
}
