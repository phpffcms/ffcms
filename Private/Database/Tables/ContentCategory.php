<?php

Illuminate\Database\Capsule\Manager::schema()->create('content_categories', function($table) {
    $table->increments('id');
    $table->string('path', 200)->unique();
    $table->text('title');
    $table->text('description')->nullable();
    $table->binary('configs')->nullable();
    $table->timestamps();
});

$now = date('Y-m-d H:i:s', time());

$cat = new stdClass();

$cat->General = [
    'title' => serialize([
        'ru' => 'Главная',
        'en' => 'General'
    ])
];

$cat->News = [
    'title' => serialize([
        'ru' => 'Новости',
        'en' => 'News'
    ]),
    'configs' => serialize([
        'showDate' => '1',
        'showCategory' => '1',
        'showAuthor' => '1',
        'showViews' => '1',
        'showComments' => '1',
        'showPoster' => '1'
    ])
];

$cat->Page = [
    'title' => serialize([
        'ru' => 'Страницы',
        'en' => 'Pages'
    ])
];

Illuminate\Database\Capsule\Manager::connection()->table('content_categories')->insert([
    ['path' => '', 'title' => $cat->General['title'], 'description' => '', 'configs' => '', 'created_at' => $now, 'updated_at' => $now],
    ['path' => 'news', 'title' => $cat->News['title'], 'description' => '', 'configs' => $cat->News['configs'], 'created_at' => $now, 'updated_at' => $now],
    ['path' => 'page', 'title' => $cat->Page['title'], 'description' => '', 'configs' => '', 'created_at' => $now, 'updated_at' => $now],
]);