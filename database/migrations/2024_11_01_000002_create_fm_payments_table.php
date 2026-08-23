<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fm_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no')->unique();
            $table->string('serial_number')->nullable();
            $table->date('posting_date');
            $table->string('transaction_type')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('mode_of_payment_id')->nullable();
            $table->string('party_type')->nullable();
            $table->string('party_id')->nullable();
            $table->string('party_name')->nullable();
            $table->unsignedBigInteger('company_bank_account_id')->nullable();
            $table->unsignedBigInteger('party_bank_account_id')->nullable();
            $table->unsignedBigInteger('account_paid_from_id')->nullable();
            $table->unsignedBigInteger('account_paid_to_id')->nullable();
            $table->unsignedBigInteger('from_currency_id')->nullable();
            $table->unsignedBigInteger('to_currency_id')->nullable();
            $table->decimal('payment_amount', 15, 2)->default(0);
            $table->decimal('total_allocation_amount', 15, 2)->default(0);
            $table->decimal('unallocated_amount', 15, 2)->default(0);
            $table->decimal('different_amount', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->string('reference_number')->nullable();
            $table->date('reference_date')->nullable();
            $table->string('payment_status')->nullable();
            $table->unsignedBigInteger('payment_request_id')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('fm_companies')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fm_payments');
    }
};
