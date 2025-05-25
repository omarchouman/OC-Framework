<?php

namespace Migrations;

use Core\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->createTable('table', function ($table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->dropTable('table');
    }
}