<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('user_providers', function($table) {
    $table->increments('id');
    $table->string('user_id');
    $table->string('provider_name', 255);
    $table->string('provider_id', 255);
    $table->timestamps();
});