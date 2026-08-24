<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $chart_of_accounts = 'chart_of_accounts';
    protected $ = ["account_name","account_name_with_abbr","account_number","accounting_type_id","parent_account_id","balance_must_be","company_id","currency_id","tax_rate","is_disabled"];


}
