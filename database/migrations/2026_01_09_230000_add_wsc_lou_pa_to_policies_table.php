<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            if (!Schema::hasColumn('policies', 'wsc')) {
                $table->decimal('wsc', 15, 2)->nullable()->after('premium');
            }
            if (!Schema::hasColumn('policies', 'lou')) {
                $table->decimal('lou', 15, 2)->nullable()->after('wsc');
            }
            if (!Schema::hasColumn('policies', 'pa')) {
                $table->decimal('pa', 15, 2)->nullable()->after('lou');
            }
        });
    }

    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn(['wsc', 'lou', 'pa']);
        });
    }
};
