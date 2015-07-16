<?php

Illuminate\Database\Capsule\Manager::schema()->create('profile_ratings', function($table) {
    $table->increments('id');
    $table->integer('target_id')->unsigned();
    $table->integer('sender_id')->unsigned();
    $table->enum('type', ['+', '-']);
    $table->timestamps();
});