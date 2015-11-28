<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('wall_answers', function($table) {
    $table->increments('id');
    $table->integer('post_id')->unsigned();
    $table->integer('user_id')->unsigned();
    $table->text('message');
    $table->timestamps();
});