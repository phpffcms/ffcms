<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('comment_posts', function($table) {
    $table->increments('id');
    $table->string('pathway', 1024);
    $table->integer('user_id')->unsigned();
    $table->string('guest_name', 100);
    $table->text('message');
    $table->string('lang', 32)->default('en');
    $table->string('ip', 64)->default('127.0.0.1'); // ipv4 and ipv6 (32 chars + 7 digits is max)
    $table->boolean('moderate')->default(false);
    $table->timestamps();
});