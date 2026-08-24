<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_tax_rules', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->string('rule_type');
            $table->unsignedBigInteger('tax_template_id');
            $table->foreign('tax_template_id')->references('id')->on('fm_tax_templates')->nullOnDelete();
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('tax_category_id')->nullable();
            $table->foreign('tax_category_id')->references('id')->on('fm_tax_categories')->nullOnDelete();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->string('billing_street')->nullable();
            $table->string('billing_house')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->unsignedBigInteger('billing_country_id')->nullable();
            $table->string('shipping_street')->nullable();
            $table->string('shipping_house')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->unsignedBigInteger('shipping_country_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_tax_rules');
    }
};
