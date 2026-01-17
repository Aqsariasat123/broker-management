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
            $table->integer('children')->nullable()->after('spouses_name');
            $table->text('children_details')->nullable()->after('children');
            $table->string('pic')->nullable()->after('passport_no');
            $table->string('industry')->nullable()->after('pic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['children', 'children_details', 'pic', 'industry']);
        });
    }
};
