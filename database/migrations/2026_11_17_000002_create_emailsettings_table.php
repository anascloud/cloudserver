<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('emailsettings')) {
            Schema::create('emailsettings', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('protocol')->nullable();
                $table->string('encryption')->nullable();
                $table->string('email')->nullable();
                $table->string('charset')->nullable();
                $table->string('smtpHost')->nullable();
                $table->unsignedInteger('smtpPort')->nullable();
                $table->string('smtpUsername')->nullable();
                $table->string('smtpPassword')->nullable();
                $table->text('signature')->nullable();
                $table->timestamps();
            });
        }

        if (DB::table('emailsettings')->count() === 0) {
            DB::table('emailsettings')->insert([
                'protocol' => 1,
                'encryption' => null,
                'email' => null,
                'charset' => 'UTF-8',
                'smtpHost' => null,
                'smtpPort' => null,
                'smtpUsername' => null,
                'smtpPassword' => null,
                'signature' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('emailsettings');
    }
};