<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->string('account_name_with_abbr')->nullable();
            $table->string('account_number')->nullable();
            $table->unsignedBigInteger('accounting_type_id')->nullable();
            $table->foreign('accounting_type_id')->references('id')->on('accounting_types')->nullOnDelete();
            $table->unsignedBigInteger('parent_account_id')->nullable();
            $table->foreign('parent_account_id')->references('id')->on('chart_of_accounts')->nullOnDelete();
            $table->string('balance_must_be')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('fm_companies')->nullOnDelete();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('fm_currencies')->nullOnDelete();
            $table->string('tax_rate')->nullable();
            $table->boolean('is_disabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
