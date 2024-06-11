<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseEvents extends Migration
{
    public function up()
    {
        DB::statement("SET GLOBAL event_scheduler = ON");
        DB::statement("CREATE EVENT delete_soft_deleted_images
ON SCHEDULE EVERY 1 MINUTE
DO
BEGIN
	DELETE FROM images
	WHERE TIMESTAMPDIFF(MINUTE,deleted_at,CURRENT_TIMESTAMP) > 1;
END");
    }

    public function down()
    {
        DB::statement("DROP EVENT delete_soft_deleted_images");
    }
}
