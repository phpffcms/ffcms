<?php

Illuminate\Database\Capsule\Manager::schema()->create('users', function($table) {
    $table->increments('id');
    $table->string('login')->unique();
    $table->string('email')->unique();
    $table->string('nick')->nullable();
    $table->string('password', 512);
    $table->tinyInteger('role_id')->default(1);
    $table->tinyInteger('is_aproved')->default(0);
    $table->string('token_data')->nullable();
    $table->string('token_ip')->nullable();
    $table->binary('custom_data')->nullable();
    $table->timestamps();
});