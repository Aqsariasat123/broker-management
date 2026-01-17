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
            Schema::create('medicals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('life_proposal_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('medical_code')->unique();

            $table->foreignId('medical_type_id')
                ->nullable()
                ->constrained('lookup_values')
                ->nullOnDelete();

            $table->string('clinic')->nullable();

            $table->date('ordered_on')->nullable();
            $table->date('completed_on')->nullable();

            $table->foreignId('status_id')
                ->nullable()
                ->constrained('lookup_values')
                ->nullOnDelete();

            $table->string('results_path')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['life_proposal_id', 'status_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicals');
    }
};
