<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $table = 'fm_bank_accounts';

    protected $fillable = [
        'bank_account_name', 'bank_account_type_id', 'bank_account_type_name',
        'bank_id', 'bank_name', 'account_number', 'iban', 'swift_code',
        'currency_id', 'currency_name', 'opening_balance', 'current_balance',
        'company_id', 'is_default', 'is_disabled',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_default' => 'boolean',
        'is_disabled' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
