<?php

Illuminate\Database\Capsule\Manager::schema()->create('blacklists', function($table) {
    $table->increments('id');
    $table->integer('user_id')->unsigned();
    $table->integer('target_id')->unsigned();
    $table->string('comment')->nullable();
    $table->timestamps();
});