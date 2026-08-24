<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_budget_distributions', function (Blueprint $table) {
            $table->id();
            $table->string('budget_distribution_name');
            $table->timestamps();
        });

        Schema::create('fm_budget_distribution_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_distribution_id');
            $table->foreign('budget_distribution_id')->references('id')->on('fm_budget_distributions')->cascadeOnDelete();
            $table->unsignedBigInteger('month_id');
            $table->string('month_name')->nullable();
            $table->decimal('budget_percentage', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_budget_distribution_details');
        Schema::dropIfExists('fm_budget_distributions');
    }
};
