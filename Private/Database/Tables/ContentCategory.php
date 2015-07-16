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

$tmp = [
    'General' => [
        'title' => serialize([
            'ru' => 'Главная',
            'en' => 'General'
        ])
    ],
    'News' => [
        'title' => serialize([
            'ru' => 'Новости',
            'en' => 'News'
        ])
    ],
    'Page' => [
        'title' => serialize([
            'ru' => 'Страницы',
            'en' => 'Pages'
        ])
    ]

];

Illuminate\Database\Capsule\Manager::connection()->table('content_categories')->insert([
    ['path' => '', 'title' => $tmp['General']['title'], 'description' => '', 'created_at' => $now, 'updated_at' => $now],
    ['path' => 'news', 'title' => $tmp['News']['title'], 'description' => '', 'created_at' => $now, 'updated_at' => $now],
    ['path' => 'page', 'title' => $tmp['Page']['title'], 'description' => '', 'created_at' => $now, 'updated_at' => $now],
]);