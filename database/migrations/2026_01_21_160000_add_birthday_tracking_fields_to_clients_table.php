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
            if (!Schema::hasColumn('clients', 'home_no')) {
                $table->string('home_no')->nullable()->after('alternate_no');
            }
            if (!Schema::hasColumn('clients', 'bday_medium')) {
                $table->string('bday_medium')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('clients', 'bday_wish_status')) {
                $table->string('bday_wish_status')->nullable()->after('bday_medium');
            }
            if (!Schema::hasColumn('clients', 'bday_date_done')) {
                $table->date('bday_date_done')->nullable()->after('bday_wish_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['home_no', 'bday_medium', 'bday_wish_status', 'bday_date_done']);
        });
    }
};
