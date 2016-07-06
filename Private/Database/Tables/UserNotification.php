<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('user_notifications', function($table) {
    $table->increments('id');
    $table->string('user_id');
    $table->string('msg', 2048);
    $table->string('uri', 2048);
    $table->binary('vars')->nullable();
    $table->boolean('readed')->default(false);
    $table->timestamps();
});