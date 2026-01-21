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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'variance')) {
                $table->decimal('variance', 15, 2)->nullable()->after('notes');
            }
            if (!Schema::hasColumn('payments', 'variance_reason')) {
                $table->string('variance_reason')->nullable()->after('variance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['variance', 'variance_reason']);
        });
    }
};
