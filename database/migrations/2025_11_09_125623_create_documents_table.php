<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('doc_id')->unique();
            $table->string('tied_to')->nullable();
            $table->string('name');
            $table->string('group')->nullable();
            $table->string('type')->nullable();
            $table->string('format')->nullable();
            $table->date('date_added')->nullable();
            $table->string('year')->nullable();
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
