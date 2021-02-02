<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->unique();
            $table->timestamp('time')->nullable();
            $table->string('location')->nullable();
            $table->json('repeat')->nullable();
            /*
             * repeat structure
             * {
             *   every {
             *      number : integer,
             *      unit : 'day | week | month | year',
             *      weekdays : ['Monday', 'Wednesday', ...]   //active only for 'week' unit
             *   }
             *   ends {   //null means "never"
             *     after : integer //number of occurences,   OR
             *     on_date : Date //year, month, and day
             *   }
             * }
             */

            $table->foreign('note_id')->references('id')
                ->on('notes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reminders');
    }
}
