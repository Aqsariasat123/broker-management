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
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'id_expiry_date')) {
                $table->date('id_expiry_date')->nullable()->after('passport_no');
            }
            if (!Schema::hasColumn('clients', 'monthly_income')) {
                $table->string('monthly_income')->nullable()->after('income_source');
            }
            if (!Schema::hasColumn('clients', 'agency')) {
                $table->string('agency')->nullable()->after('signed_up');
            }
            if (!Schema::hasColumn('clients', 'agent')) {
                $table->string('agent')->nullable()->after('agency');
            }
            if (!Schema::hasColumn('clients', 'source_name')) {
                $table->string('source_name')->nullable()->after('source');
            }
            if (!Schema::hasColumn('clients', 'has_vehicle')) {
                $table->boolean('has_vehicle')->default(false)->after('source_name');
            }
            if (!Schema::hasColumn('clients', 'has_house')) {
                $table->boolean('has_house')->default(false)->after('has_vehicle');
            }
            if (!Schema::hasColumn('clients', 'has_business')) {
                $table->boolean('has_business')->default(false)->after('has_house');
            }
            if (!Schema::hasColumn('clients', 'has_boat')) {
                $table->boolean('has_boat')->default(false)->after('has_business');
            }
            if (!Schema::hasColumn('clients', 'notes')) {
                $table->text('notes')->nullable()->after('has_boat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['id_expiry_date', 'monthly_income', 'agency', 'agent', 'source_name', 'has_vehicle', 'has_house', 'has_business', 'has_boat', 'notes']);
        });
    }
};
