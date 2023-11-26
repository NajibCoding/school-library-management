<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id()->unique();
            $table->integer('parent_id')->default(0);
            $table->string('name', 100);
            $table->string('slug', 200)->nullable();
            $table->string('url', 200)->nullable();
            $table->string('icon', 50)->nullable();
            $table->integer('sequence')->default(0);
            $table->text('roles')->default(new Expression("(JSON_ARRAY('SUPERADMIN'))"));
            $table->text('permission')->nullable();
            $table->tinyInteger('has_child')->default(0);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
