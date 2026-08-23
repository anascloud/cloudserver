<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('product')->unique();
            $table->string('warehouse')->nullable();
            $table->string('quantity')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
