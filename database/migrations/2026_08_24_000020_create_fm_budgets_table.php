<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('budget_name');
            $table->unsignedBigInteger('budget_against_id');
            $table->foreign('budget_against_id')->references('id')->on('fm_budget_againsts');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->unsignedBigInteger('fiscal_year_id');
            $table->foreign('fiscal_year_id')->references('id')->on('fm_fiscal_years');
            $table->unsignedBigInteger('budget_distribution_id');
            $table->foreign('budget_distribution_id')->references('id')->on('fm_budget_distributions');
            $table->unsignedBigInteger('cost_center_id')->nullable();
            $table->foreign('cost_center_id')->references('id')->on('fm_cost_centers')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('fm_budget_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_id');
            $table->foreign('budget_id')->references('id')->on('fm_budgets')->cascadeOnDelete();
            $table->unsignedBigInteger('chart_of_account_id');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
            $table->decimal('budget_amount', 20, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_budget_details');
        Schema::dropIfExists('fm_budgets');
    }
};
