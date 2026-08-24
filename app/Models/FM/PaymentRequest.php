<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $fm_payment_requests = 'fm_payment_requests';
    protected $ = ["payment_request_no","payment_request_type","company_id","transaction_date","mode_of_payment_id","party_type","party_id","party_name","reference_type","reference_number","amount","outstanding_amount","party_account_currency_id","transaction_currency_id","bank_account_id","bank_name","bank_account_number","description","comments","status"];


}
