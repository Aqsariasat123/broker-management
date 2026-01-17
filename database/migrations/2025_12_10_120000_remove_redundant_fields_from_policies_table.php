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
        Schema::table('policies', function (Blueprint $table) {
            // Remove redundant legacy varchar fields
            if (Schema::hasColumn('policies', 'client_name')) {
                $table->dropColumn('client_name');
            }
            if (Schema::hasColumn('policies', 'insurer')) {
                $table->dropColumn('insurer');
            }
            if (Schema::hasColumn('policies', 'policy_class')) {
                $table->dropColumn('policy_class');
            }
            if (Schema::hasColumn('policies', 'policy_plan')) {
                $table->dropColumn('policy_plan');
            }
            if (Schema::hasColumn('policies', 'policy_status')) {
                $table->dropColumn('policy_status');
            }
            if (Schema::hasColumn('policies', 'biz_type')) {
                $table->dropColumn('biz_type');
            }
            if (Schema::hasColumn('policies', 'frequency')) {
                $table->dropColumn('frequency');
            }
            if (Schema::hasColumn('policies', 'pay_plan')) {
                $table->dropColumn('pay_plan');
            }
            if (Schema::hasColumn('policies', 'agency')) {
                $table->dropColumn('agency');
            }
            // Remove duplicate policy identifier
            if (Schema::hasColumn('policies', 'policy_id')) {
                $table->dropColumn('policy_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            // Re-add columns if needed for rollback
            $table->string('client_name')->nullable()->after('client_id');
            $table->string('insurer')->nullable()->after('insurer_id');
            $table->string('policy_class')->nullable()->after('policy_class_id');
            $table->string('policy_plan')->nullable()->after('policy_plan_id');
            $table->string('policy_status')->nullable()->after('policy_status_id');
            $table->string('biz_type')->nullable()->after('business_type_id');
            $table->string('frequency')->nullable()->after('frequency_id');
            $table->string('pay_plan')->nullable()->after('pay_plan_lookup_id');
            $table->string('agency')->nullable()->after('agency_id');
            $table->string('policy_id')->nullable()->after('policy_code');
        });
    }
};
