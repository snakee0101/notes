<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteTagTable extends Migration
{
    public function up()
    {
        Schema::create('note_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('note_id');
            $table->unsignedBigInteger('tag_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('note_tag');
    }
}
