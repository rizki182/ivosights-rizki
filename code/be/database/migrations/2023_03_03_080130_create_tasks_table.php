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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string("name", 30);
            $table->integer("project_id")->nullable();
            $table->string("level", 30); // urgent, normal, low
            $table->text("description");
            $table->string("status", 30); // open. closed
            $table->dateTime("reminder");
            $table->dateTime("deadline");
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
        Schema::dropIfExists('tasks');
    }
};
