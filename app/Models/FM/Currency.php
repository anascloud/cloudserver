<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fm_currencies = 'fm_currencies';
    protected $ = ["currency_name","symbol","fraction","units","small_value","number_format"];
}
