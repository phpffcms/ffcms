<?php

Illuminate\Database\Capsule\Manager::schema()->create('comment_answers', function($table) {
    $table->increments('id');
    $table->integer('comment_id')->unsigned();
    $table->integer('user_id')->unsigned();
    $table->string('guest_name', 100);
    $table->text('message');
    $table->timestamps();
});