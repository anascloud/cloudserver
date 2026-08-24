<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFmCountriesTable extends Migration
{
    public function up()
    {
        Schema::create('fm_countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_name');
            $table->string('country_code', 10)->nullable();
            $table->string('date_format', 50)->nullable();
            $table->string('time_format', 50)->nullable();
            $table->string('time_zone', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fm_countries');
    }
}
