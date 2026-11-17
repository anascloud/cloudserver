<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('createdBy')->nullable();
                $table->boolean('online')->default(false);
                $table->boolean('isBlock')->default(false);
                $table->timestamps();

                $table->index('customer_id');
                $table->index('createdBy');
            });
        }

        if (!Schema::hasTable('message_details')) {
            Schema::create('message_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('messageId');
                $table->text('messageContent')->nullable();
                $table->unsignedBigInteger('sender')->nullable();
                $table->unsignedBigInteger('createdBy')->nullable();
                $table->timestamps();

                $table->index('messageId');
                $table->index('sender');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('message_details');
        Schema::dropIfExists('messages');
    }
};