<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('note_id');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('note_id')->references('id')
                                            ->on('notes')
                                            ->onDelete('cascade');

            $table->foreign('tag_id')->references('id')
                                            ->on('tags')
                                            ->onDelete('cascade');
            $table->unique(['note_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_tag');
    }
}
