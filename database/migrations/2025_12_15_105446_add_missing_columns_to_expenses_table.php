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
            // Add expense_id if it doesn't exist (for custom expense ID like EX1001)
            if (!Schema::hasColumn('expenses', 'expense_id')) {
                $table->string('expense_id')->unique()->nullable()->after('id');
            }
            
            // Add mode_of_payment if it doesn't exist
            if (!Schema::hasColumn('expenses', 'mode_of_payment')) {
                $table->string('mode_of_payment')->nullable()->after('category_id');
            }
            
            // Add receipt_no if it doesn't exist
            if (!Schema::hasColumn('expenses', 'receipt_no')) {
                $table->string('receipt_no')->nullable()->after('mode_of_payment');
            }
            
            // Add expense_notes if it doesn't exist
            if (!Schema::hasColumn('expenses', 'expense_notes')) {
                $table->text('expense_notes')->nullable()->after('receipt_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'expense_notes')) {
                $table->dropColumn('expense_notes');
            }
            if (Schema::hasColumn('expenses', 'receipt_no')) {
                $table->dropColumn('receipt_no');
            }
            if (Schema::hasColumn('expenses', 'mode_of_payment')) {
                $table->dropColumn('mode_of_payment');
            }
            if (Schema::hasColumn('expenses', 'expense_id')) {
                $table->dropColumn('expense_id');
            }
        });
    }
};
