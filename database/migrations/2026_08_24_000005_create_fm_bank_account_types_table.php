<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_bank_account_types', function (Blueprint $table) {
            $table->id();
            $table->string('bank_account_type_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_bank_account_types');
    }
};
