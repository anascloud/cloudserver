<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAndConditions extends Model
{
    use HasFactory;

    protected $table = 'fm_terms_and_conditions';
    protected $fillable = ["terms_and_condition_name","is_disabled","is_selling","is_buying","description","is_active","is_deleted"];
}
