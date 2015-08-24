<?php

Illuminate\Database\Capsule\Manager::schema()->create('invites', function($table) {
    $table->increments('id');
    $table->string('token', 128)->unique();
    $table->string('email')->unique();
    $table->timestamps();
});