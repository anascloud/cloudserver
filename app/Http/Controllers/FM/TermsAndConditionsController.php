<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\TermsAndConditions;

class TermsAndConditionsController extends ApiCrudController
{
    protected $model = TermsAndConditions::class;

    protected $resource = 'TermsAndConditions';

    protected $resourcePlural = 'TermsAndConditionss';

    protected $searchable = ['terms_and_condition_name'];

    protected $rules = [
        'terms_and_condition_name' => 'sometimes|required_without:termsAndConditionName|string|max:255',
        'termsAndConditionName' => 'sometimes|required_without:terms_and_condition_name|string|max:255',
        'is_disabled' => 'sometimes|boolean',
        'isDisabled' => 'sometimes|boolean',
        'is_selling' => 'sometimes|boolean',
        'isSelling' => 'sometimes|boolean',
        'is_buying' => 'sometimes|boolean',
        'isBuying' => 'sometimes|boolean',
    ];
}
