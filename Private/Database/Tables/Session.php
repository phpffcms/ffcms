<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('sessions', function($table) {
    $table->string('sess_id', 128)->index()->primary()  ;
    $table->binary('sess_data');
    $table->string('sess_lifetime', 16); // shutout to laravel, if i make it like "integer" it automaticlly add "primary" key for it, hate this!!!
    $table->string('sess_time', 16);
    $table->timestamps();
});