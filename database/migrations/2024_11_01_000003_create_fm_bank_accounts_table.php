<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fm_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_account_name');
            $table->unsignedBigInteger('bank_account_type_id')->nullable();
            $table->string('bank_account_type_name')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('currency_name')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_disabled')->default(false);

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('fm_companies')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fm_bank_accounts');
    }
};
