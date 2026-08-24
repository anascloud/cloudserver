<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_payment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('payment_request_no')->nullable();
            $table->string('payment_request_type');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->date('transaction_date');
            $table->unsignedBigInteger('mode_of_payment_id');
            $table->foreign('mode_of_payment_id')->references('id')->on('fm_mode_of_payments');
            $table->string('party_type');
            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_name')->nullable();
            $table->string('reference_type')->nullable();
            $table->string('reference_number')->nullable();
            $table->decimal('amount', 20, 4);
            $table->decimal('outstanding_amount', 20, 4)->nullable();
            $table->unsignedBigInteger('party_account_currency_id');
            $table->foreign('party_account_currency_id')->references('id')->on('fm_currencies');
            $table->unsignedBigInteger('transaction_currency_id');
            $table->foreign('transaction_currency_id')->references('id')->on('fm_currencies');
            $table->unsignedBigInteger('bank_account_id');
            $table->foreign('bank_account_id')->references('id')->on('fm_bank_accounts');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->text('description')->nullable();
            $table->text('comments')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_payment_requests');
    }
};
