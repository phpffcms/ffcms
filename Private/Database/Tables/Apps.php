<?php

Illuminate\Database\Capsule\Manager::schema()->create('apps', function($table) {
    $table->increments('id');
    $table->enum('type', ['widget', 'app']);
    $table->string('sys_name');
    $table->text('name');
    $table->binary('configs')->nullable();
    $table->tinyInteger('disabled')->default(0);
    $table->timestamps();
});

$now = date('Y-m-d H:i:s', time());

$configs = new stdClass();

$configs->user = serialize([
    'register_type' => 1, // 0 = invites, 1 = full-open, 2 = mail-validation
    'login_captcha' => 0
]);


Illuminate\Database\Capsule\Manager::connection()->table('apps')->insert([
    ['type' => 'app', 'sys_name' => 'user', 'name' => 'a:2:{s:2:"en";s:13:"User identify";s:2:"ru";s:51:"Идентификация пользователя";}', 'configs' => $configs->user, 'created_at' => $now, 'updated_at' => $now]
]);
