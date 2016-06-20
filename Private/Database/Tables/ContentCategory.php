<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('content_categories', function($table) {
    $table->increments('id');
    $table->string('path', 200)->unique();
    $table->text('title');
    $table->text('description')->nullable();
    $table->binary('configs')->nullable();
    $table->timestamps();
});