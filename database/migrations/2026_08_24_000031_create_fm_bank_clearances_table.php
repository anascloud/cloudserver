<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_bank_clearances', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no');
            $table->date('posting_date');
            $table->string('transaction_type');
            $table->string('party_type');
            $table->string('party_id')->nullable();
            $table->string('party_name')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->unsignedBigInteger('mode_of_payment_id')->nullable();
            $table->unsignedBigInteger('party_bank_account_id')->nullable();
            $table->unsignedBigInteger('company_bank_account_id')->nullable();
            $table->unsignedBigInteger('account_paid_from_id')->nullable();
            $table->unsignedBigInteger('account_paid_to_id')->nullable();
            $table->unsignedBigInteger('from_currency_id')->nullable();
            $table->unsignedBigInteger('to_currency_id')->nullable();
            $table->decimal('payment_amount', 20, 4);
            $table->decimal('total_allocation_amount', 20, 4)->nullable();
            $table->decimal('unallocated_amount', 20, 4)->nullable();
            $table->decimal('different_amount', 20, 4)->nullable();
            $table->decimal('total_tax', 20, 4)->nullable();
            $table->string('reference_number')->nullable();
            $table->date('reference_date')->nullable();
            $table->string('payment_status')->nullable();
            $table->date('bank_clearence_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_bank_clearances');
    }
};
