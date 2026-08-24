<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('asset_maintenance_serial_number')->nullable();
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('fm_assets');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        Schema::create('fm_asset_maintenance_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_maintenance_id');
            $table->foreign('asset_maintenance_id')->references('id')->on('fm_asset_maintenances')->cascadeOnDelete();
            $table->string('asset_maintenance_task_name')->nullable();
            $table->string('maintenance_status')->nullable();
            $table->string('maintenance_repetition')->nullable();
            $table->unsignedBigInteger('assigned_to_id')->nullable();
            $table->string('assigned_to_name')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_asset_maintenance_details');
        Schema::dropIfExists('fm_asset_maintenances');
    }
};
