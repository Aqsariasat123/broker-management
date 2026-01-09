<?php
// database/migrations/2024_01_01_create_clients_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_type'); // Individual, Business, Company
            $table->string('nin_bcrn')->nullable();
            $table->date('dob_dor')->nullable();
            $table->string('mobile_no');
            $table->string('wa')->nullable();
            $table->string('district')->nullable();
            $table->string('occupation')->nullable();
            $table->string('source');
            $table->string('status'); // Active, Inactive, etc.
            $table->date('signed_up');
            $table->string('employer')->nullable();
            $table->string('clid')->unique();
            $table->string('contact_person')->nullable();
            $table->string('income_source')->nullable();
            $table->boolean('married')->default(false);
            $table->string('spouses_name')->nullable();
            $table->string('alternate_no')->nullable();
            $table->string('email_address')->nullable();
            $table->text('location')->nullable();
            $table->string('island')->nullable();
            $table->string('country')->nullable();
            $table->string('po_box_no')->nullable();
            $table->boolean('pep')->default(false);
            $table->text('pep_comment')->nullable();
            $table->string('image')->nullable();
            $table->string('salutation')->nullable();
            $table->string('first_name');
            $table->string('other_names')->nullable();
            $table->string('surname');
            $table->string('passport_no')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}