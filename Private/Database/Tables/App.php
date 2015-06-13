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
$names = new stdClass();

$configs->user = serialize([
    'registrationType' => 1, // 0 = invites, 1 = validation via email, 2 = full open without validation
    'captchaOnLogin' => 0,
    'captchaOnRegister' => 1
]);

$configs->profile = serialize([]);

$names->user = serialize([
    'en' => 'User identity',
    'ru' => 'Идентификация пользователя'
]);

$names->profile = serialize([
    'en' => 'User profiles',
    'ru' => 'Профили пользователей'
]);


Illuminate\Database\Capsule\Manager::connection()->table('apps')->insert([
    ['type' => 'app', 'sys_name' => 'User', 'name' => $names->user, 'configs' => $configs->user, 'created_at' => $now, 'updated_at' => $now],
    ['type' => 'app', 'sys_name' => 'Profile', 'name' => $names->profile, 'configs' => $configs->profile, 'created_at' => $now, 'updated_at' => $now]
]);
