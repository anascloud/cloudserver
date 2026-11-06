<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCategory extends Model
{
    use HasFactory;

    protected $table = 'fm_tax_categories';
    protected $fillable = ["tax_category_name","zatca_category_id","is_active"];
}
