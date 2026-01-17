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
            // Only add the column if it doesn't already exist
            if (!Schema::hasColumn('incomes', 'statement_no')) {
                $table->string('statement_no', 191)->nullable()->after('commission_statement_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            // Only drop the column if it exists
            if (Schema::hasColumn('incomes', 'statement_no')) {
                $table->dropColumn('statement_no');
            }
        });
    }
};