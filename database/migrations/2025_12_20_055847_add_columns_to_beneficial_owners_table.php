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
        Schema::table('beneficial_owners', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('beneficial_owners', 'dob')) {
                $table->date('dob')->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('beneficial_owners', 'nin_passport_no')) {
                $table->string('nin_passport_no')->nullable()->after('dob');
            }
            if (!Schema::hasColumn('beneficial_owners', 'country')) {
                $table->string('country')->nullable()->after('nin_passport_no');
            }
            if (!Schema::hasColumn('beneficial_owners', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('country');
            }
            if (!Schema::hasColumn('beneficial_owners', 'status')) {
                $table->string('status')->nullable()->after('expiry_date');
            }
            if (!Schema::hasColumn('beneficial_owners', 'position')) {
                $table->string('position')->nullable()->after('status');
            }
            // Add shares column (keep ownership_percentage for backward compatibility)
            if (!Schema::hasColumn('beneficial_owners', 'shares')) {
                $table->decimal('shares', 5, 2)->nullable()->after('position');
            }
            if (!Schema::hasColumn('beneficial_owners', 'pep')) {
                $table->boolean('pep')->default(false)->after('shares');
            }
            if (!Schema::hasColumn('beneficial_owners', 'pep_details')) {
                $table->text('pep_details')->nullable()->after('pep');
            }
            if (!Schema::hasColumn('beneficial_owners', 'date_added')) {
                $table->date('date_added')->nullable()->after('pep_details');
            }
            if (!Schema::hasColumn('beneficial_owners', 'removed')) {
                $table->boolean('removed')->default(false)->after('date_added');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficial_owners', function (Blueprint $table) {
            // Remove columns if they exist
            $columns = ['dob', 'nin_passport_no', 'country', 'expiry_date', 'status', 'position', 'shares', 'pep', 'pep_details', 'date_added', 'removed'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('beneficial_owners', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
