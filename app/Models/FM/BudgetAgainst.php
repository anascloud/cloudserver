<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetAgainst extends Model
{
    use HasFactory;

    protected $table = 'fm_budget_againsts';
    protected $fillable = ["budget_against_name"];
}
