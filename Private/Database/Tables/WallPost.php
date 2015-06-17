<?php

Illuminate\Database\Capsule\Manager::schema()->create('wall_posts', function($table) {
    $table->increments('id');
    $table->integer('target_id')->unsigned();
    $table->integer('sender_id')->unsigned();
    $table->text('message');
    $table->timestamps();
});