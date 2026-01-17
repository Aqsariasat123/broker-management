<?php
// database/migrations/2024_01_01_create_lookup_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLookupTables extends Migration
{
    public function up()
    {
        Schema::create('lookup_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('lookup_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lookup_category_id')->constrained()->onDelete('cascade');
            $table->integer('seq');
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('code')->nullable();
            $table->timestamps();
            
            $table->unique(['lookup_category_id', 'seq']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lookup_values');
        Schema::dropIfExists('lookup_categories');
    }
}