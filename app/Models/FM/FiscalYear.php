<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    use HasFactory;

    protected $table = 'fm_fiscal_years';
    protected $fillable = ["year_range","start_date","end_date"];
}
