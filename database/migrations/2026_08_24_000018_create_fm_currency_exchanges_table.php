<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_currency_exchanges', function (Blueprint $table) {
            $table->id();
            $table->string('currency_exchange_no')->nullable();
            $table->date('date_of_establishment');
            $table->unsignedBigInteger('currency_from_id');
            $table->foreign('currency_from_id')->references('id')->on('fm_currencies');
            $table->unsignedBigInteger('currency_to_id');
            $table->foreign('currency_to_id')->references('id')->on('fm_currencies');
            $table->decimal('exchange_rate', 20, 8);
            $table->boolean('is_purchase')->default(false);
            $table->boolean('is_selling')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_currency_exchanges');
    }
};
