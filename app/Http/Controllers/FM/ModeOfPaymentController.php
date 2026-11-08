<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\ModeOfPayment;

class ModeOfPaymentController extends ApiCrudController
{
    protected $model = ModeOfPayment::class;

    protected $resource = 'ModeOfPayment';

    protected $resourcePlural = 'ModeOfPayments';

    protected $searchable = ['mode_of_payment_name'];

    protected $rules = [
        'mode_of_payment_name' => 'sometimes|required_without:modeOfPaymentName|string|max:255',
        'modeOfPaymentName' => 'sometimes|required_without:mode_of_payment_name|string|max:255',
        'mode_of_payment_type' => 'sometimes|nullable|string|max:255',
        'modeOfPaymentType' => 'sometimes|nullable|string|max:255',
        'company_id' => 'sometimes|nullable|integer',
        'companyId' => 'sometimes|nullable|integer',
        'chart_of_account_id' => 'sometimes|nullable|integer',
        'chartOfAccountId' => 'sometimes|nullable|integer',
        'is_active' => 'sometimes|boolean',
        'isActive' => 'sometimes|boolean',
    ];
}
