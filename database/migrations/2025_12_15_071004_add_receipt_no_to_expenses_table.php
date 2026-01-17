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
        Schema::table('expenses', function (Blueprint $table) {
            // Add expense_id if it doesn't exist (for backward compatibility)
            if (!Schema::hasColumn('expenses', 'expense_id')) {
                $table->string('expense_id')->unique()->nullable()->after('id');
            }
            
            // Drop category column if it exists (migrating to category_id)
            if (Schema::hasColumn('expenses', 'category')) {
                $table->dropColumn('category');
            }
            
            // Add mode_of_payment if it doesn't exist (for backward compatibility)
            if (!Schema::hasColumn('expenses', 'mode_of_payment')) {
                $table->string('mode_of_payment')->nullable()->after('category_id');
            }
            
            // Add receipt_no if it doesn't exist
            if (!Schema::hasColumn('expenses', 'receipt_no')) {
                $table->string('receipt_no')->nullable()->after('mode_of_payment');
            }
            
            // Add receipt_path if it doesn't exist
            if (!Schema::hasColumn('expenses', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('receipt_no');
            }
            
            // Add expense_notes if it doesn't exist (for backward compatibility)
            if (!Schema::hasColumn('expenses', 'expense_notes')) {
                $table->text('expense_notes')->nullable()->after('receipt_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'receipt_no')) {
                $table->dropColumn('receipt_no');
            }
            if (Schema::hasColumn('expenses', 'receipt_path')) {
                $table->dropColumn('receipt_path');
            }
        });
    }
};
