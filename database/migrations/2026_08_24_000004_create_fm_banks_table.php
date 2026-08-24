<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_banks', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('bank_website')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('contact_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_banks');
    }
};
