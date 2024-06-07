<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id');
            $table->string('image_path');
            $table->string('thumbnail_small_path');
            $table->string('thumbnail_large_path');
            $table->longText('recognized_text')->nullable();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE images ADD image LONGBLOB");
        DB::statement("ALTER TABLE images ADD thumbnail_small LONGBLOB");
        DB::statement("ALTER TABLE images ADD thumbnail_large LONGBLOB");
    }

    public function down()
    {
        Schema::dropIfExists('images');
    }
}
