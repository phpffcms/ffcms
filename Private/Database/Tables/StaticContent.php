<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('static_content', function($table) {
    $table->increments('id');
    $table->string('sys_name')->unique();
    $table->text('content');
    $table->boolean('enabled')->default(false);
    $table->timestamps();
});