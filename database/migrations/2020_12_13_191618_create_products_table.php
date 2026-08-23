<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('code', 100)->nullable();
            $table->double('actualPrice', 8, 2)->default(0);
            $table->string('sellPrice', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('unit', 100)->nullable();
            $table->string('size', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->string('thumbnail', 100)->nullable();
            $table->string('attributes', 100)->nullable();
            $table->unsignedBigInteger('user_id')->comment('Created By User');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
