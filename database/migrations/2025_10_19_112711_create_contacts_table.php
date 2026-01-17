<?php
// database/migrations/2024_01_01_create_contacts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('contact_name');
            $table->string('contact_no')->nullable();
            $table->string('type'); // Lead, Prospect, Contact
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->date('acquired')->nullable();
            $table->string('source'); // Direct, Referral, Walk In, etc.
            $table->string('status'); // Not Contacted, In Discussion, Proposal Made, etc.
            $table->string('rank')->nullable(); // VIP, High, Medium, Low
            $table->date('first_contact')->nullable();
            $table->date('next_follow_up')->nullable();
            $table->string('coid')->nullable();
            $table->date('dob')->nullable();
            $table->string('salutation'); // Mr, Mrs, Ms, Miss
            $table->string('source_name')->nullable();
            $table->string('agency')->nullable();
            $table->string('agent')->nullable();
            $table->text('address')->nullable();
            $table->string('email_address')->nullable();
            $table->string('contact_id')->unique();
            $table->decimal('savings_budget', 10, 2)->nullable();
            $table->boolean('married')->default(false);
            $table->integer('children')->default(0);
            $table->text('children_details')->nullable();
            $table->string('vehicle')->nullable();
            $table->string('house')->nullable();
            $table->string('business')->nullable();
            $table->text('other')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}