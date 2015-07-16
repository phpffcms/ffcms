<?php

Illuminate\Database\Capsule\Manager::schema()->create('profiles', function($table) {
    $table->increments('id');
    $table->integer('user_id')->unsigned()->unique();
    $table->string('nick')->nullable();
    $table->tinyInteger('sex')->default(0);
    $table->date('birthday')->nullable();
    $table->string('city')->nullable();
    $table->string('hobby')->nullable();
    $table->integer('rating')->default(0);
    $table->string('phone')->nullable();
    $table->string('url')->nullable();
    $table->text('custom_data')->nullable();
    $table->timestamps();
});