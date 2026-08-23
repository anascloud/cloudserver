<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->default('0.00');
            $table->string('description', 100)->nullable();
            $table->string('leadStatus', 100)->nullable();
            $table->string('fullName', 100)->nullable();
            $table->string('email', 300)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('address', 100);
            $table->string('company', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->string('assignedTo', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('feedback', 100)->nullable();
            $table->string('industry', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
