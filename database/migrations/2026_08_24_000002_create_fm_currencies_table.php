<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('currency_name');
            $table->string('symbol')->nullable();
            $table->string('fraction')->nullable();
            $table->string('units')->nullable();
            $table->string('small_value')->nullable();
            $table->string('number_format')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_currencies');
    }
};
