<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('asset_category_name');
            $table->unsignedBigInteger('fixed_asset_account_id')->nullable();
            $table->foreign('fixed_asset_account_id')->references('id')->on('chart_of_accounts')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_asset_categories');
    }
};
