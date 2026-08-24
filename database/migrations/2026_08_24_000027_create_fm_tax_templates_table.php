<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_tax_templates', function (Blueprint $table) {
            $table->id();
            $table->string('tax_template_name');
            $table->unsignedBigInteger('tax_template_type_id')->nullable();
            $table->string('template_type')->nullable();
            $table->unsignedBigInteger('tax_category_id')->nullable();
            $table->foreign('tax_category_id')->references('id')->on('fm_tax_categories')->nullOnDelete();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('fm_companies')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fm_tax_template_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_template_id');
            $table->foreign('tax_template_id')->references('id')->on('fm_tax_templates')->cascadeOnDelete();
            $table->string('tax_type')->nullable();
            $table->unsignedBigInteger('chart_of_account_id');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
            $table->decimal('tax_rate', 10, 4)->nullable();
            $table->decimal('tax_amount', 20, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_tax_template_details');
        Schema::dropIfExists('fm_tax_templates');
    }
};
