<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('contents', function($table) {
    $table->increments('id');
    $table->text('title');
    $table->mediumText('text');
    $table->string('path');
    $table->integer('category_id');
    $table->integer('author_id');
    $table->string('poster', 255)->nullable();
    $table->boolean('display')->default(1);
    $table->text('meta_title')->nullable();
    $table->text('meta_keywords')->nullable();
    $table->text('meta_description')->nullable();
    $table->integer('views')->default(0);
    $table->integer('rating')->default(0);
    $table->string('source', 1024);
    $table->string('comment_hash', 128)->nullable();
    $table->timestamps();
    $table->softDeletes();
});