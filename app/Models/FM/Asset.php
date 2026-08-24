<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fm_assets = 'fm_assets';
    protected $ = ["asset_name","asset_serial_number","product_id","product_name","company_id","asset_category_id","asset_location_id","asset_owner_name","maintainer_id","maintainer_name","department_id","department_name","is_existing_asset","is_composite_asset","purchase_date","purchase_receipt_id","purchase_receipt_number","purchase_invoice_id","purchase_invoice_no","available_for_use_date","gross_purchase_amount","asset_quantity","is_calculated_depreciation","opening_accumulated_depreciation","opening_number_of_book_depreciation","is_fully_depreciated","finance_book_id","finance_book_name","depreciation_method","total_depreciation_period","frequency_of_depreciation","depreciation_start_date","expected_residual_value","insurance_policy_number","insurance_company_name","insurance_policy_start_date","insurance_policy_end_date","insurance_amount","is_maintenance_required","asset_status","asset_image_url"];


}
