<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('roles', function($table) {
    $table->increments('id');
    $table->string('name');
    $table->text('permissions');
    $table->timestamps();
});

$now = date('Y-m-d H:i:s', time());

Illuminate\Database\Capsule\Manager::connection($connectName)->table('roles')->insert([
    ['name' => 'OnlyRead', 'permissions' => '', 'created_at' => $now, 'updated_at' => $now],
    ['name' => 'User', 'permissions' => 'global/write;global/file', 'created_at' => $now, 'updated_at' => $now],
    ['name' => 'Moderator', 'permissions' => 'global/write;global/modify;global/file', 'created_at' => $now, 'updated_at' => $now],
    ['name' => 'Admin', 'permissions' => 'global/all', 'created_at' => $now, 'updated_at' => $now]
]);
