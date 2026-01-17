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
        Schema::create('beneficial_owners', function (Blueprint $table) {
            $table->id();
            $table->string('owner_code')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('relationship')->nullable();
            $table->decimal('ownership_percentage', 5, 2)->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('poa_document_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficial_owners');
    }
};
