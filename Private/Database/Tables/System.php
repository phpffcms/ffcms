<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('systems', function($table) {
    $table->increments('id');
    $table->string('var', 1024);
    $table->binary('data');
    $table->timestamps();
});