<?php

use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Type\Object;
use Ffcms\Core\Helper\Url;

/** @var $routes array */

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Routing')
];

$this->title = __('Routing');

?>

<h1><?= __('Routing scheme') ?></h1>
<hr />
<div class="row">
    <div class="col-md-6">
        <h2><?= __('Static(alias) routes') ?></h2>
        <?php
        $staticItems = [];
        $dynamicItems = [];
        if (Object::isArray($routes['Alias'])) {
            foreach ($routes['Alias'] as $env => $route) {
                if (Object::isArray($route)) {
                    foreach ($route as $source => $target) {
                        $staticItems[] = [
                            ['text' => $env],
                            ['text' => $source],
                            ['text' => $target],
                            ['text' => '<i class="fa fa-remove"></i>', 'html' => true]
                        ];
                    }
                }
            }
        }
        if (Object::isArray($routes['Callback'])) {
            foreach ($routes['Callback'] as $env => $route) {
                if (Object::isArray($route)) {
                    foreach ($route as $source => $target) {
                        $dynamicItems[] = [
                            ['text' => $env],
                            ['text' => $source],
                            ['text' => $target],
                            ['text' => '<i class="fa fa-remove"></i>', 'html' => true]
                        ];
                    }
                }
            }
        }
        ?>
        <?=
            Table::display([
                'table' => ['class' => 'table table-bordered'],
                'thead' => [
                    'titles' => [
                        ['text' => __('Environment')],
                        ['text' => __('Source path')],
                        ['text' => __('Target path')],
                        ['text' => __('Actions')]
                    ]
                ],
                'tbody' => [
                    'items' => $staticItems
                ]])
        ?>
    </div>
    <div class="col-md-6">
        <h2><?= __('Dynamic routes') ?></h2>
        <?=
        Table::display([
            'table' => ['class' => 'table table-bordered'],
            'thead' => [
                'titles' => [
                    ['text' => __('Environment')],
                    ['text' => __('Inject controller')],
                    ['text' => __('Target class')],
                    ['text' => __('Actions')]
                ]
            ],
            'tbody' => [
                'items' => $dynamicItems
            ]])
        ?>
    </div>
</div>
