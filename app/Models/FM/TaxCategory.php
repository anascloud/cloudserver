<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCategory extends Model
{
    use HasFactory;

    protected $fm_tax_categories = 'fm_tax_categories';
    protected $ = ["tax_category_name","zatca_category_id","is_active"];
}
