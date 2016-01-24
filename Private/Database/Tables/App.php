<?php

Illuminate\Database\Capsule\Manager::schema($connectName)->create('apps', function($table) {
    $table->increments('id');
    $table->enum('type', ['widget', 'app']);
    $table->string('sys_name');
    $table->text('name');
    $table->binary('configs')->nullable();
    $table->boolean('disabled')->default(false);
    $table->double('version', 6, 1)->default(0.1); // from 0.1 to 99999.9
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

$configs->profile = serialize([
    'guestView' => 1,
    'wallPostOnPage' => 5,
    'delayBetweenPost' => 30,
    'rating' => 1,
    'ratingDelay' => 60 * 60 * 24,
    'usersOnPage' => 10
]);

$configs->comments = serialize([
    'perPage' => 10,
    'delay' => 60,
    'minLength' => 10,
    'maxLength' => 10,
    'guestAdd' => 0,
    'editTime' => 180
]);

$names->user = serialize([
    'en' => 'User identity',
    'ru' => 'Идентификация пользователя'
]);

$names->profile = serialize([
    'en' => 'User profiles',
    'ru' => 'Профили пользователей'
]);

$names->content = serialize([
    'en' => 'Content',
    'ru' => 'Контент'
]);

$configs->content = serialize([
    'itemPerCategory' => 10,
    'userAdd' => 0,
    'multiCategories' => 1,
    'keywordsAsTags' => 1,
    'rss' => 1,
    'gallerySize' => 500,
    'galleryResize' => 150
]);

$names->comments = serialize([
    'en' => 'Comments',
    'ru' => 'Комментарии'
]);


Illuminate\Database\Capsule\Manager::connection($connectName)->table('apps')->insert([
    ['type' => 'app', 'sys_name' => 'User', 'name' => $names->user, 'configs' => $configs->user, 'version' => 0.1, 'created_at' => $now, 'updated_at' => $now],
    ['type' => 'app', 'sys_name' => 'Profile', 'name' => $names->profile, 'configs' => $configs->profile, 'version' => 0.1, 'created_at' => $now, 'updated_at' => $now],
    ['type' => 'app', 'sys_name' => 'Content', 'name' => $names->content, 'configs' => $configs->content, 'version' => 0.1, 'created_at' => $now, 'updated_at' => $now],
    ['type' => 'widget', 'sys_name' => 'Comments', 'name' => $names->comments, 'configs' => $configs->comments, 'version' => 0.1, 'created_at' => $now, 'updated_at' => $now]
]);
