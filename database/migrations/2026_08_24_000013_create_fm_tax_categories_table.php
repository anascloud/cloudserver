<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_tax_categories', function (Blueprint $table) {
            $table->id();
            $table->string('tax_category_name');
            $table->unsignedBigInteger('zatca_category_id')->nullable();
            $table->foreign('zatca_category_id')->references('id')->on('fm_zatca_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_tax_categories');
    }
};
