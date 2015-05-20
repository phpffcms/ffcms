<?php

Illuminate\Database\Capsule\Manager::schema()->create('walls', function($table) {
    $table->increments('id');
    $table->integer('user_id');
    $table->integer('sender_id');
    $table->text('message');
    $table->timestamps();
});