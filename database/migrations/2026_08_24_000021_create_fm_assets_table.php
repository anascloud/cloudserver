<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_name')->nullable();
            $table->string('asset_serial_number')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->unsignedBigInteger('asset_category_id');
            $table->foreign('asset_category_id')->references('id')->on('fm_asset_categories');
            $table->unsignedBigInteger('asset_location_id')->nullable();
            $table->foreign('asset_location_id')->references('id')->on('fm_asset_locations')->nullOnDelete();
            $table->string('asset_owner_name')->nullable();
            $table->unsignedBigInteger('maintainer_id')->nullable();
            $table->string('maintainer_name')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('department_name')->nullable();
            $table->boolean('is_existing_asset')->default(false);
            $table->boolean('is_composite_asset')->default(false);
            $table->date('purchase_date')->nullable();
            $table->unsignedBigInteger('purchase_receipt_id')->nullable();
            $table->string('purchase_receipt_number')->nullable();
            $table->unsignedBigInteger('purchase_invoice_id')->nullable();
            $table->string('purchase_invoice_no')->nullable();
            $table->date('available_for_use_date')->nullable();
            $table->decimal('gross_purchase_amount', 20, 4)->nullable();
            $table->decimal('asset_quantity', 10, 2)->nullable();
            $table->boolean('is_calculated_depreciation')->default(false);
            $table->decimal('opening_accumulated_depreciation', 20, 4)->nullable();
            $table->decimal('opening_number_of_book_depreciation', 10, 2)->nullable();
            $table->boolean('is_fully_depreciated')->default(false);
            $table->unsignedBigInteger('finance_book_id')->nullable();
            $table->string('finance_book_name')->nullable();
            $table->string('depreciation_method')->nullable();
            $table->integer('total_depreciation_period')->nullable();
            $table->integer('frequency_of_depreciation')->nullable();
            $table->date('depreciation_start_date')->nullable();
            $table->decimal('expected_residual_value', 20, 4)->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->string('insurance_company_name')->nullable();
            $table->date('insurance_policy_start_date')->nullable();
            $table->date('insurance_policy_end_date')->nullable();
            $table->decimal('insurance_amount', 20, 4)->nullable();
            $table->boolean('is_maintenance_required')->default(false);
            $table->string('asset_status')->nullable();
            $table->string('asset_image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_assets');
    }
};
