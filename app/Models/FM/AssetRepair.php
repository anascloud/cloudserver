<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRepair extends Model
{
    use HasFactory;

    protected $fm_asset_repairs = 'fm_asset_repairs';
    protected $ = ["asset_repair_serial_number","asset_id","company_id","failure_date","completion_date","repair_date","purchase_invoice_no","expense_account_id","repair_cost","repair_description","repair_status"];


}
