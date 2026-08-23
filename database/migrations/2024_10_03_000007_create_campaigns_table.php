<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 100)->default('0.00');
            $table->date('deadline')->nullable();
            $table->string('company', 100)->nullable();
            $table->string('service', 100)->nullable();
            $table->string('description', 300)->nullable();
            $table->string('contact', 100)->nullable();
            $table->string('source', 100)->comment('Created By User');
            $table->string('type', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
