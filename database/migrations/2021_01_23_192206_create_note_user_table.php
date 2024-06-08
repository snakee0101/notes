<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteUserTable extends Migration
{
    public function up()
    {
        Schema::create('note_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id');
            $table->foreignId('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('note_user');
    }
}
