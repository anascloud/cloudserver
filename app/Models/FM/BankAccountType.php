<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccountType extends Model
{
    use HasFactory;

    protected $table = 'fm_bank_account_types';
    protected $fillable = ["bank_account_type_name"];
}
