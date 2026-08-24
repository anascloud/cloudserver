<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->string('asset_depreciation_serial_number')->nullable();
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('fm_assets');
            $table->unsignedBigInteger('finance_book_id')->nullable();
            $table->string('finance_book_name')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->string('depreciation_method')->nullable();
            $table->integer('total_depreciation_period')->nullable();
            $table->integer('frequency_of_depreciation')->nullable();
            $table->decimal('expected_value', 20, 4)->nullable();
            $table->string('asset_status')->nullable();
            $table->timestamps();
        });

        Schema::create('fm_asset_depreciation_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_depreciation_id');
            $table->foreign('asset_depreciation_id')->references('id')->on('fm_asset_depreciations')->cascadeOnDelete();
            $table->date('schedule_date')->nullable();
            $table->decimal('depreciation_amount', 20, 4)->nullable();
            $table->decimal('accumulated_depreciation_amount', 20, 4)->nullable();
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->foreign('journal_id')->references('id')->on('fm_journal_entries')->nullOnDelete();
            $table->boolean('is_journal_created')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_asset_depreciation_schedules');
        Schema::dropIfExists('fm_asset_depreciations');
    }
};
