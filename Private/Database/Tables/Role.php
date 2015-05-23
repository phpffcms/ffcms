<?php

Illuminate\Database\Capsule\Manager::schema()->create('roles', function($table) {
    $table->increments('id');
    $table->string('name');
    $table->text('permissions');
    $table->timestamps();
});

$now = date('Y-m-d H:i:s', time());

echo $now;

Illuminate\Database\Capsule\Manager::connection()->table('roles')->insert([
    ['name' => 'User', 'permissions' => 'global/write;', 'created_at' => $now, 'updated_at' => $now],
    ['name' => 'Moderator', 'permissions' => 'global/write;global/modify;', 'created_at' => $now, 'updated_at' => $now],
    ['name' => 'Admin', 'permissions' => 'global/all', 'created_at' => $now, 'updated_at' => $now]
]);
