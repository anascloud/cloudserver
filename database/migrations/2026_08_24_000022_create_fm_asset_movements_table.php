<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_asset_movements', function (Blueprint $table) {
            $table->id();
            $table->string('asset_movement_serial_number')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->date('transaction_date');
            $table->string('purpose_of_movement');
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('fm_assets');
            $table->unsignedBigInteger('asset_location_id')->nullable();
            $table->foreign('asset_location_id')->references('id')->on('fm_asset_locations')->nullOnDelete();
            $table->unsignedBigInteger('from_employee_id')->nullable();
            $table->string('from_employee_name')->nullable();
            $table->unsignedBigInteger('to_employee_id')->nullable();
            $table->string('to_employee_name')->nullable();
            $table->unsignedBigInteger('targeted_location_id')->nullable();
            $table->string('targeted_location_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_asset_movements');
    }
};
