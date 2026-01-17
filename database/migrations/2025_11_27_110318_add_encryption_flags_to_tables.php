<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add encryption flag to debit_notes table
        if (Schema::hasTable('debit_notes') && !Schema::hasColumn('debit_notes', 'is_encrypted')) {
            Schema::table('debit_notes', function (Blueprint $table) {
                $table->boolean('is_encrypted')->default(false)->after('document_path');
            });
        }

        // Add encryption flag to payments table
        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'is_encrypted')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->boolean('is_encrypted')->default(false)->after('receipt_path');
            });
        }

        // Add encryption flag to documents table if it exists
        if (Schema::hasTable('documents') && !Schema::hasColumn('documents', 'is_encrypted')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->boolean('is_encrypted')->default(false)->after('file_path');
            });
        }

        // Add encryption flag to clients table for sensitive fields
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (!Schema::hasColumn('clients', 'id_document_encrypted')) {
                    if (Schema::hasColumn('clients', 'id_document_path')) {
                        $table->boolean('id_document_encrypted')->default(false)->after('id_document_path');
                    } else {
                        $table->boolean('id_document_encrypted')->default(false);
                    }
                }
                if (!Schema::hasColumn('clients', 'poa_document_encrypted')) {
                    if (Schema::hasColumn('clients', 'poa_document_path')) {
                        $table->boolean('poa_document_encrypted')->default(false)->after('poa_document_path');
                    } else {
                        $table->boolean('poa_document_encrypted')->default(false);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('debit_notes') && Schema::hasColumn('debit_notes', 'is_encrypted')) {
            Schema::table('debit_notes', function (Blueprint $table) {
                $table->dropColumn('is_encrypted');
            });
        }

        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'is_encrypted')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('is_encrypted');
            });
        }

        if (Schema::hasTable('documents') && Schema::hasColumn('documents', 'is_encrypted')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('is_encrypted');
            });
        }

        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (Schema::hasColumn('clients', 'id_document_encrypted')) {
                    $table->dropColumn('id_document_encrypted');
                }
                if (Schema::hasColumn('clients', 'poa_document_encrypted')) {
                    $table->dropColumn('poa_document_encrypted');
                }
            });
        }
    }
};
