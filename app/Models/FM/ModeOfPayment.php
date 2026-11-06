<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeOfPayment extends Model
{
    use HasFactory;

    protected $table = 'fm_mode_of_payments';
    protected $fillable = ["mode_of_payment_name","mode_of_payment_type","company_id","chart_of_account_id","is_active","comment"];
}
