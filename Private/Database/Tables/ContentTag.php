<?php
Illuminate\Database\Capsule\Manager::schema($connectName)->create('content_tags', function($table) {
    $table->increments('id');
    $table->integer('content_id')->unsigned();
    $table->string('lang', 36)->default('en');
    $table->string('tag', 1024);
});