<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_journal_templates', function (Blueprint $table) {
            $table->id();
            $table->string('journal_template_title');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->unsignedBigInteger('journal_type_id')->nullable();
            $table->foreign('journal_type_id')->references('id')->on('fm_journal_entry_types')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('fm_journal_template_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_template_id');
            $table->foreign('journal_template_id')->references('id')->on('fm_journal_templates')->cascadeOnDelete();
            $table->unsignedBigInteger('chart_of_account_id');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_journal_template_accounts');
        Schema::dropIfExists('fm_journal_templates');
    }
};
