<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetForeignKeysAndConstraints extends Migration
{
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->foreign('note_id')
                ->references('id')
                ->on('notes')
                ->onDelete('cascade');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->foreign('note_id')
                ->references('id')
                ->on('notes')
                ->onDelete('cascade');
        });

        Schema::table('note_user', function (Blueprint $table) {
            $table->unique(['note_id', 'user_id']);

            $table->foreign('note_id')->references('id')
                ->on('notes')
                ->onDelete('cascade');
        });

        Schema::table('note_tag', function (Blueprint $table) {
            $table->foreign('note_id')->references('id')
                ->on('notes')
                ->onDelete('cascade');

            $table->foreign('tag_id')->references('id')
                ->on('tags')
                ->onDelete('cascade');

            $table->unique(['note_id', 'tag_id']);
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('reminders', function (Blueprint $table) {
            $table->foreign('note_id')->references('id')
                ->on('notes')
                ->onDelete('cascade');

            $table->unique(['note_id', 'user_id']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['name', 'user_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('checklist_id')
                ->references('id')
                ->on('checklists')
                ->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['checklist_id']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('reminders', function (Blueprint $table) {
            $table->dropForeign(['note_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
        });

        Schema::table('note_tag', function (Blueprint $table) {
            $table->dropForeign(['note_id']);
            $table->dropForeign(['tag_id']);
        });

        Schema::table('note_user', function (Blueprint $table) {
            $table->dropForeign(['note_id']);
        });

        Schema::table('links', function (Blueprint $table) {
            $table->dropForeign(['note_id']);
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign(['note_id']);
        });
    }
}
