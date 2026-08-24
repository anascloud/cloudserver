<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyExchange extends Model
{
    use HasFactory;

    protected $fm_currency_exchanges = 'fm_currency_exchanges';
    protected $ = ["currency_exchange_no","date_of_establishment","currency_from_id","currency_to_id","exchange_rate","is_purchase","is_selling"];


}
