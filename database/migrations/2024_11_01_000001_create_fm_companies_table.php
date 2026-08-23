<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fm_companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('abbreviation')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('logo_url')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('tax_no')->nullable();
            $table->string('domain')->nullable();
            $table->date('date_of_establishment')->nullable();
            $table->string('parent_company')->nullable();
            $table->date('date_of_incorporation')->nullable();
            $table->text('company_description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            $table->unsignedBigInteger('default_bank_account_id')->nullable();
            $table->unsignedBigInteger('default_payable_account_id')->nullable();
            $table->unsignedBigInteger('default_cash_account_id')->nullable();
            $table->unsignedBigInteger('default_cost_of_goods_sold_account_id')->nullable();
            $table->unsignedBigInteger('default_receivable_account_id')->nullable();
            $table->unsignedBigInteger('default_income_account_id')->nullable();
            $table->unsignedBigInteger('default_expense_account_id')->nullable();
            $table->unsignedBigInteger('round_off_account_id')->nullable();
            $table->unsignedBigInteger('round_off_cost_center_id')->nullable();
            $table->unsignedBigInteger('default_deferred_revenue_account_id')->nullable();
            $table->unsignedBigInteger('default_deferred_expense_account_id')->nullable();
            $table->unsignedBigInteger('write_off_account')->nullable();
            $table->unsignedBigInteger('default_payment_discount_account_id')->nullable();
            $table->unsignedBigInteger('exchange_gain_loss_account_id')->nullable();
            $table->unsignedBigInteger('unrealized_gain_loss_account_id')->nullable();
            $table->unsignedBigInteger('default_cost_center_id')->nullable();
            $table->unsignedBigInteger('unrealized_profit_loss_account_id')->nullable();

            $table->unsignedBigInteger('accumulated_depreciation_account')->nullable();
            $table->unsignedBigInteger('depreciation_expense_account')->nullable();
            $table->unsignedBigInteger('gain_loss_account_on_assets_disposal')->nullable();
            $table->unsignedBigInteger('assets_depreciation_cost_center')->nullable();
            $table->unsignedBigInteger('capital_work_in_progress_account')->nullable();
            $table->unsignedBigInteger('asset_valuation_account')->nullable();
            $table->unsignedBigInteger('assets_received_but_not_billed_account')->nullable();

            $table->unsignedBigInteger('default_finance_book')->nullable();
            $table->text('buying_terms')->nullable();
            $table->text('selling_terms')->nullable();
            $table->decimal('monthly_sales_target', 15, 2)->nullable();
            $table->string('warehouse_sales_return')->nullable();
            $table->decimal('total_monthly_sales', 15, 2)->nullable();
            $table->decimal('credit_limit', 15, 2)->nullable();

            $table->boolean('is_perpetual_inventory')->nullable()->default(false);
            $table->boolean('is_provisional_accounting')->nullable()->default(false);
            $table->string('inventory_account')->nullable();
            $table->string('stock_received_but_not_billed_account')->nullable();
            $table->string('stock_adjustment_account')->nullable();
            $table->string('provisional_account')->nullable();
            $table->string('expanses_include_in_valuation')->nullable();
            $table->string('in_transit_warehouse_account')->nullable();
            $table->string('operating_cost_account')->nullable();

            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_disabled')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fm_companies');
    }
};
