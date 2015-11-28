<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('comment_posts', function($table) {
    $table->increments('id');
    $table->string('pathway', 1024);
    $table->integer('user_id')->unsigned();
    $table->string('guest_name', 100);
    $table->text('message');
    $table->string('lang', 32)->default('en');
    $table->timestamps();
});