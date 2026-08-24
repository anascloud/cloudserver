<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_asset_repairs', function (Blueprint $table) {
            $table->id();
            $table->string('asset_repair_serial_number')->nullable();
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('fm_assets');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->date('failure_date');
            $table->date('completion_date')->nullable();
            $table->date('repair_date')->nullable();
            $table->string('purchase_invoice_no')->nullable();
            $table->unsignedBigInteger('expense_account_id')->nullable();
            $table->foreign('expense_account_id')->references('id')->on('chart_of_accounts')->nullOnDelete();
            $table->decimal('repair_cost', 20, 4)->nullable();
            $table->text('repair_description')->nullable();
            $table->string('repair_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_asset_repairs');
    }
};
