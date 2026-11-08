<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\Currency;

class CurrencyController extends ApiCrudController
{
    protected $model = Currency::class;

    protected $resource = 'Currency';

    protected $resourcePlural = 'Currencys';

    protected $searchable = ['currencyName'];

    protected $rules = [
        'currencyName' => 'sometimes|required_without:currencyName|string|max:255',
        'currencyName' => 'sometimes|required_without:currencyName|string|max:255',
    ];
}
