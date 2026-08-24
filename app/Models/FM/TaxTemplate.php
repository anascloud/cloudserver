<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxTemplate extends Model
{
    use HasFactory;

    protected $fm_tax_templates = 'fm_tax_templates';
    protected $ = ["tax_template_name","tax_template_type_id","template_type","tax_category_id","company_id","is_active"];

    public function details()
    {
        return $this->hasMany($this->getDetailClass(), $this->getForeignKey());
    }
}
