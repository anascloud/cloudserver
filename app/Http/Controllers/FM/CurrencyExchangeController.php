<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\CurrencyExchange;

class CurrencyExchangeController extends ApiCrudController
{
    protected $model = CurrencyExchange::class;

    protected $resource = 'CurrencyExchange';

    protected $resourcePlural = 'CurrencyExchanges';

    protected $searchable = ['currency_exchange_no'];

    protected $rules = [
        'currency_exchange_no' => 'sometimes|required|string|max:255',
        'currencyExchangeNo' => 'sometimes|required|string|max:255',
        'date_of_establishment' => 'sometimes|nullable|date',
        'dateOfEstablishment' => 'sometimes|nullable|date',
        'currency_from_id' => 'sometimes|required|integer',
        'currencyFromId' => 'sometimes|required|integer',
        'currency_to_id' => 'sometimes|required|integer',
        'currencyToId' => 'sometimes|required|integer',
        'exchange_rate' => 'sometimes|nullable|numeric',
        'exchangeRate' => 'sometimes|nullable|numeric',
        'is_purchase' => 'sometimes|boolean',
        'isPurchase' => 'sometimes|boolean',
        'is_selling' => 'sometimes|boolean',
        'isSelling' => 'sometimes|boolean',
    ];
}
