<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrawingsTable extends Migration
{
    public function up()
    {
        Schema::create('drawings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id');
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE drawings ADD image LONGBLOB");
        DB::statement("ALTER TABLE drawings ADD thumbnail LONGBLOB");
    }

    public function down()
    {
        Schema::dropIfExists('drawings');
    }
}
