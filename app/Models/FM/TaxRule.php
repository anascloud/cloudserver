<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    use HasFactory;

    protected $fm_tax_rules = 'fm_tax_rules';
    protected $ = ["serial_number","rule_type","tax_template_id","customer_id","customer_name","supplier_id","supplier_name","product_id","product_name","tax_category_id","company_id","valid_from","valid_to","billing_street","billing_house","billing_zip","billing_city","billing_state","billing_country_id","shipping_street","shipping_house","shipping_zip","shipping_city","shipping_state","shipping_country_id"];


}
