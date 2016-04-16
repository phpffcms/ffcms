<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('user_logs', function($table) {
    $table->increments('id');
    $table->string('user_id');
    $table->string('type');
    $table->string('message', 2048);
    $table->timestamps();
});