<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankReconciliation extends Model
{
    use HasFactory;

    protected $table = 'fm_bank_reconciliations';
    protected $fillable = ["company_id","bank_account_id","from_date","to_date"];


}
