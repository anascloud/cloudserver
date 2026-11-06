<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZatcaCategory extends Model
{
    use HasFactory;

    protected $table = 'fm_zatca_categories';
    protected $fillable = ["zatca_category_name"];
}
