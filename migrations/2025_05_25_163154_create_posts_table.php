<?php

namespace Migrations;

use Core\Database\Migration;

class CreatePostsTable extends Migration
{
    public function up()
    {
        $this->createTable('posts', function ($table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('author');
            $table->string('slug');
            $table->string('image');
            $table->string('category');
            $table->string('tags');
            $table->string('status');
            $table->string('meta_title');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->dropTable('posts');
    }
} 