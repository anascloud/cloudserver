<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_terms_and_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('terms_and_condition_name');
            $table->boolean('is_disabled')->default(false);
            $table->boolean('is_selling')->default(false);
            $table->boolean('is_buying')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_terms_and_conditions');
    }
};
