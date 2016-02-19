<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('feedback_answers', function($table) {
    $table->increments('id');
    $table->integer('feedback_id')->unsigned();
    $table->string('name', 100);
    $table->string('email');
    $table->text('message');
    $table->boolean('is_admin')->default(false);
    $table->integer('user_id')->unsigned()->default(0);
    $table->string('ip', 64)->default('127.0.0.1'); // ipv6 & ipv4
    $table->timestamps();
});