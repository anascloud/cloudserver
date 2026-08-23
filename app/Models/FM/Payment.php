<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'fm_payments';

    protected $fillable = [
        'payment_no', 'serial_number', 'posting_date', 'transaction_type',
        'company_id', 'mode_of_payment_id', 'party_type', 'party_id', 'party_name',
        'company_bank_account_id', 'party_bank_account_id',
        'account_paid_from_id', 'account_paid_to_id',
        'from_currency_id', 'to_currency_id',
        'payment_amount', 'total_allocation_amount', 'unallocated_amount',
        'different_amount', 'total_tax', 'reference_number', 'reference_date',
        'payment_status', 'payment_request_id', 'cheque_no', 'cheque_date',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'reference_date' => 'date',
        'cheque_date' => 'date',
        'payment_amount' => 'decimal:2',
        'total_allocation_amount' => 'decimal:2',
        'unallocated_amount' => 'decimal:2',
        'different_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
