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
        Schema::table('incomes', function (Blueprint $table) {
            // Remove category column if it exists
            if (Schema::hasColumn('incomes', 'category')) {
                $table->dropColumn('category');
            }
            // Add category_id if it doesn't exist
            if (!Schema::hasColumn('incomes', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('description')->constrained('lookup_values')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (Schema::hasColumn('incomes', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });
    }
};
