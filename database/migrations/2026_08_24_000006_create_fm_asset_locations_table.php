<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_asset_locations', function (Blueprint $table) {
            $table->id();
            $table->string('asset_location_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_asset_locations');
    }
};
