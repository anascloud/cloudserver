<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fm_budgets = 'fm_budgets';
    protected $ = ["budget_name","budget_against_id","company_id","fiscal_year_id","budget_distribution_id","cost_center_id"];

    public function details()
    {
        return $this->hasMany($this->getDetailClass(), $this->getForeignKey());
    }
}
