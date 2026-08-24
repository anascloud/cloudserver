<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    use HasFactory;

    protected $fm_bank_statements = 'fm_bank_statements';
    protected $ = ["company_id","bank_account_id","currency_id","bs_file"];

    public function details()
    {
        return $this->hasMany($this->getDetailClass(), $this->getForeignKey());
    }
}
