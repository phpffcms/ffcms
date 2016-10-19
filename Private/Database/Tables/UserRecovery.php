<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('user_recoveries', function($table) {
    $table->increments('id');
    $table->integer('user_id')->unsigned();
    $table->string('password', 512);
    $table->string('token', 128)->nullable();
    $table->boolean('archive')->default(false);
    $table->timestamps();
});