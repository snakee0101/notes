<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id');
            $table->text('text');
            $table->boolean('completed')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
