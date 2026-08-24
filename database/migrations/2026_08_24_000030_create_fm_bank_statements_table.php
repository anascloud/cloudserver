<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_bank_statements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->foreign('bank_account_id')->references('id')->on('fm_bank_accounts')->nullOnDelete();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('fm_currencies')->nullOnDelete();
            $table->string('bs_file')->nullable();
            $table->timestamps();
        });

        Schema::create('fm_bank_statement_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_statement_id');
            $table->foreign('bank_statement_id')->references('id')->on('fm_bank_statements')->cascadeOnDelete();
            $table->date('transaction_date')->nullable();
            $table->decimal('amount', 20, 4)->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_bank_statement_details');
        Schema::dropIfExists('fm_bank_statements');
    }
};
