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
            if (Schema::hasColumn('incomes', 'bank_statement_path')) {
                $table->dropColumn('bank_statement_path');
            }
            if (Schema::hasColumn('incomes', 'document_path')) {
                $table->dropColumn('document_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'bank_statement_path')) {
                $table->string('bank_statement_path')->nullable()->after('statement_no');
            }
            if (!Schema::hasColumn('incomes', 'document_path')) {
                $table->string('document_path')->nullable()->after('bank_statement_path');
            }
        });
    }
};
