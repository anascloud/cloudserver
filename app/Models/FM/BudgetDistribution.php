<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetDistribution extends Model
{
    use HasFactory;

    protected $table = 'fm_budget_distributions';
    protected $fillable = ["budget_distribution_name"];

    public function details()
    {
        return $this->hasMany($this->getDetailClass(), $this->getForeignKey());
    }
}
