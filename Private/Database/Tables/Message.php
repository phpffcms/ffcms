<?php

Illuminate\Database\Capsule\Manager::schema()->create('messages', function($table) {
    $table->bigIncrements('id');
    $table->integer('target_id')->unsigned();
    $table->integer('sender_id')->unsigned();
    $table->text('message');
    $table->boolean('readed')->default(false);
    $table->timestamps();
});