<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankClearance extends Model
{
    use HasFactory;

    protected $fm_bank_clearances = 'fm_bank_clearances';
    protected $ = ["payment_no","posting_date","transaction_type","party_type","party_id","party_name","company_id","mode_of_payment_id","party_bank_account_id","company_bank_account_id","account_paid_from_id","account_paid_to_id","from_currency_id","to_currency_id","payment_amount","total_allocation_amount","unallocated_amount","different_amount","total_tax","reference_number","reference_date","payment_status","bank_clearence_date"];


}
