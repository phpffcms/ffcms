<?php

Illuminate\Database\Capsule\Manager::schema()->create('sessions', function($table) {
    $table->bigIncrements('id');
    $table->string('token')->unique();
    $table->text('data')->nullable();
    $table->timestamps();
});