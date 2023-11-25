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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            // $table->timestamp('email_verified_at')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedBigInteger('role_id')->default(0);
            $table->string('password');
            $table->rememberToken();
            $table->dateTime('last_login')->nullable();
            $table->string('reset_password_token', 255)->nullable();
            $table->dateTime('valid_reset_password_token_until')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
