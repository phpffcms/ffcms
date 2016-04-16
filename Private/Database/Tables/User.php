<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('users', function($table) {
    $table->increments('id');
    $table->string('login')->unique();
    $table->string('email')->unique();
    $table->string('password', 512);
    $table->tinyInteger('role_id')->default(2); // 1 = onlyRead(same as guest), 2 = user, 3 = moder, 4 = adm
    $table->string('approve_token', 128)->default(0);
    $table->timestamps();
});