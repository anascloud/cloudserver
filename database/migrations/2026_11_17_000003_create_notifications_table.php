<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('type')->nullable();
                $table->unsignedBigInteger('referenceId')->nullable();
                $table->unsignedTinyInteger('status')->default(2);
                $table->text('description')->nullable();
                $table->unsignedBigInteger('createdBy')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};