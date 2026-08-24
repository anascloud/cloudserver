<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    use HasFactory;

    protected $fm_fiscal_years = 'fm_fiscal_years';
    protected $ = ["year_range","start_date","end_date"];
}
