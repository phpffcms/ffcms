<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('systems', function($table) {
    $table->increments('id');
    $table->string('var', 1024);
    $table->binary('data');
    $table->timestamps();
});

$now = date('Y-m-d H:i:s', time());

Illuminate\Database\Capsule\Manager::connection($connectName)->table('systems')->insert([
    ['var' => 'version', 'data' => '3.0.0', 'created_at' => $now, 'updated_at' => $now] // should be updated on install/update procedure
]);