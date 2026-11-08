<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\Bank;

class BankController extends ApiCrudController
{
    protected $model = Bank::class;

    protected $resource = 'Bank';

    protected $resourcePlural = 'Banks';

    protected $searchable = ['bank_name'];

    protected $rules = [
        'bank_name' => 'sometimes|required_without:bankName|string|max:255',
        'bankName' => 'sometimes|required_without:bank_name|string|max:255',
        'bank_website' => 'sometimes|nullable|string|max:255',
        'bankWebsite' => 'sometimes|nullable|string|max:255',
        'swift_code' => 'sometimes|nullable|string|max:255',
        'swiftCode' => 'sometimes|nullable|string|max:255',
        'routing_number' => 'sometimes|nullable|string|max:255',
        'routingNumber' => 'sometimes|nullable|string|max:255',
        'contact_number' => 'sometimes|nullable|string|max:255',
        'contactNumber' => 'sometimes|nullable|string|max:255',
    ];
}
