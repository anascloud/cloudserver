<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('journal_no')->nullable();
            $table->unsignedBigInteger('journal_type_id');
            $table->foreign('journal_type_id')->references('id')->on('fm_journal_entry_types');
            $table->date('posting_date');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('fm_companies');
            $table->unsignedBigInteger('journal_template_id')->nullable();
            $table->foreign('journal_template_id')->references('id')->on('fm_journal_templates')->nullOnDelete();
            $table->string('reference_no')->nullable();
            $table->date('reference_date')->nullable();
            $table->decimal('total_debit', 20, 4)->default(0);
            $table->decimal('total_credit', 20, 4)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });

        Schema::create('fm_journal_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->foreign('journal_entry_id')->references('id')->on('fm_journal_entries')->cascadeOnDelete();
            $table->unsignedBigInteger('chart_of_account_id');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
            $table->decimal('debit', 20, 4)->default(0);
            $table->decimal('credit', 20, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_journal_details');
        Schema::dropIfExists('fm_journal_entries');
    }
};
