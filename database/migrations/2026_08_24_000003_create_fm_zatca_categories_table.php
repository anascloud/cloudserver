<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_zatca_categories', function (Blueprint $table) {
            $table->id();
            $table->string('zatca_category_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_zatca_categories');
    }
};
