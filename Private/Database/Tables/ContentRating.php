<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('content_ratings', function($table) {
    $table->increments('id');
    $table->integer('content_id');
    $table->integer('user_id');
    $table->string('type')->default('unknown');
    $table->timestamps();
});