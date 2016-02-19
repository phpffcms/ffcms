<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('feedback_posts', function($table) {
    $table->increments('id');
    $table->string('name', 100);
    $table->string('email');
    $table->text('message');
    $table->boolean('readed')->default(false);
    $table->boolean('closed')->default(false);
    $table->string('hash', 128);
    $table->integer('user_id')->unsigned()->default(0);
    $table->string('ip', 64)->default('127.0.0.1'); // ipv6 & ipv4
    $table->timestamps();
});