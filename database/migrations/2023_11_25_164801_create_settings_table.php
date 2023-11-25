<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->enum('type', ['input', 'text', 'list', 'file']);
            $table->text('value')->nullable();
            $table->enum('autoload', ['yes', 'no'])->default('yes');
            $table->text('enum')->nullable();
            $table->text('file_allowed_mimes')->nullable()->comment('if you want to using 2 or more mimes validation use comma for separator');
            $table->text('file_allowed_max_size')->nullable()->comment('null for unvalidate file size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
