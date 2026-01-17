<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tax_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_statement_id')->constrained('commission_statements')->cascadeOnDelete();
            $table->string('tax_ref_id')->unique();
            $table->string('filing_period');
            $table->date('filed_on')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('amount_due', 15, 2)->nullable();
            $table->decimal('amount_paid', 15, 2)->nullable();
            $table->string('supporting_doc_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_returns');
    }
};
