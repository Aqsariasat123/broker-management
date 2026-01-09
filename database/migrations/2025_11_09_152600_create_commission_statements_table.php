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
        Schema::create('commission_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_note_id')->nullable()->constrained('commission_notes')->nullOnDelete();
            $table->string('com_stat_id')->unique();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('net_commission', 15, 2)->nullable();
            $table->decimal('tax_withheld', 15, 2)->nullable();
            $table->string('attachment_path')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_statements');
    }
};
