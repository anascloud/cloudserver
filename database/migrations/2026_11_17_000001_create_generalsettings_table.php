<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('generalsettings')) {
            Schema::create('generalsettings', function (Blueprint $table) {
                $table->id();
                $table->string('appcompany_name')->nullable();
                $table->string('defaultEmail')->nullable();
                $table->string('mobile')->nullable();
                $table->string('currency')->nullable();
                $table->string('street')->nullable();
                $table->string('house')->nullable();
                $table->string('zip')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->text('defaultInvoiceTerms')->nullable();
                $table->timestamps();
            });
        }

        if (DB::table('generalsettings')->count() === 0) {
            DB::table('generalsettings')->insert([
                'appcompany_name' => null,
                'defaultEmail' => null,
                'mobile' => null,
                'currency' => null,
                'street' => null,
                'house' => null,
                'zip' => null,
                'city' => null,
                'state' => null,
                'defaultInvoiceTerms' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('generalsettings');
    }
};
