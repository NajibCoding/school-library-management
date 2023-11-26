<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('status', 255)->nullable();
            $table->string('method', 255)->nullable();
            $table->string('ip', 255)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->text('pathname')->nullable();
            $table->text('referral_url')->nullable();
            $table->text('query')->nullable();
            $table->longText('content_request')->nullable();
            $table->longText('content_response')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->char('id_task', 20)->index()->nullable();
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
        Schema::dropIfExists('access_logs');
    }
};
