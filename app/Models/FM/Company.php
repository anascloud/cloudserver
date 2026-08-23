<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'fm_companies';

    protected $fillable = [
        'company_name', 'abbreviation', 'country_id', 'logo_url', 'currency_id',
        'tax_no', 'domain', 'date_of_establishment', 'parent_company',
        'date_of_incorporation', 'company_description', 'phone_number', 'email', 'website',
        'default_bank_account_id', 'default_payable_account_id', 'default_cash_account_id',
        'default_cost_of_goods_sold_account_id', 'default_receivable_account_id',
        'default_income_account_id', 'default_expense_account_id', 'round_off_account_id',
        'round_off_cost_center_id', 'default_deferred_revenue_account_id',
        'default_deferred_expense_account_id', 'write_off_account',
        'default_payment_discount_account_id', 'exchange_gain_loss_account_id',
        'unrealized_gain_loss_account_id', 'default_cost_center_id',
        'unrealized_profit_loss_account_id',
        'accumulated_depreciation_account', 'depreciation_expense_account',
        'gain_loss_account_on_assets_disposal', 'assets_depreciation_cost_center',
        'capital_work_in_progress_account', 'asset_valuation_account',
        'assets_received_but_not_billed_account',
        'default_finance_book', 'buying_terms', 'selling_terms',
        'monthly_sales_target', 'warehouse_sales_return', 'total_monthly_sales', 'credit_limit',
        'is_perpetual_inventory', 'is_provisional_accounting',
        'inventory_account', 'stock_received_but_not_billed_account',
        'stock_adjustment_account', 'provisional_account',
        'expanses_include_in_valuation', 'in_transit_warehouse_account', 'operating_cost_account',
        'is_enabled', 'is_default', 'is_disabled',
    ];

    protected $casts = [
        'date_of_establishment' => 'date',
        'date_of_incorporation' => 'date',
        'monthly_sales_target' => 'decimal:2',
        'total_monthly_sales' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'is_perpetual_inventory' => 'boolean',
        'is_provisional_accounting' => 'boolean',
        'is_enabled' => 'boolean',
        'is_default' => 'boolean',
        'is_disabled' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'company_id');
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'company_id');
    }
}
