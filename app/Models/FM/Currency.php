<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'fm_currencies';
    protected $fillable = ["currencyName","symbol","fraction","units","small_value","number_format"];
}
