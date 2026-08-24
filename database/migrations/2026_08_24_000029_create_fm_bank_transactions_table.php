<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('bank_transaction_code')->nullable();
            $table->unsignedBigInteger('bank_account_id');
            $table->foreign('bank_account_id')->references('id')->on('fm_bank_accounts');
            $table->string('transaction_type');
            $table->date('transaction_date');
            $table->decimal('amount', 20, 4)->nullable();
            $table->string('status')->nullable();
            $table->string('bank_transaction_status_name')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('fm_currencies');
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->decimal('total_allocated_amount', 20, 4)->nullable();
            $table->decimal('total_un_allocated_amount', 20, 4)->nullable();
            $table->string('party_type')->nullable();
            $table->string('party_name')->nullable();
            $table->string('party_account_number')->nullable();
            $table->string('party_iban')->nullable();
            $table->decimal('deposit', 20, 4)->nullable();
            $table->decimal('withdraw', 20, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('fm_bank_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_transaction_id');
            $table->foreign('bank_transaction_id')->references('id')->on('fm_bank_transactions')->cascadeOnDelete();
            $table->string('payment_type')->nullable();
            $table->string('payment_code')->nullable();
            $table->decimal('allocated_amount', 20, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_bank_transaction_details');
        Schema::dropIfExists('fm_bank_transactions');
    }
};
