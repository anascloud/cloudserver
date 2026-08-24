<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;

    protected $fm_bank_transactions = 'fm_bank_transactions';
    protected $ = ["bank_transaction_code","bank_account_id","transaction_type","transaction_date","amount","status","bank_transaction_status_name","company_id","currency_id","reference_number","description","total_allocated_amount","total_un_allocated_amount","party_type","party_name","party_account_number","party_iban","deposit","withdraw"];

    public function details()
    {
        return $this->hasMany($this->getDetailClass(), $this->getForeignKey());
    }
}
