<?php

Illuminate\Database\Capsule\Manager::schema()->create('profile_fields', function($table) {
    $table->increments('id');
    $table->enum('type', ['text', 'link']);
    $table->text('name');
    $table->string('reg_exp', 512)->nullable();
    $table->tinyInteger('reg_cond')->default(0);
    $table->timestamps();
});