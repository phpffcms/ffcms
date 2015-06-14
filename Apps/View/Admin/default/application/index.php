<?php

/** @var $this object */
/** @var $apps object */
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Helper\Url;

$this->title = __('Applications');
$this->breadcrumbs = [
    Url::to(['main/index']) => __('Main'),
    __('Applications')
];
?>
<h1><?= __('List of applications'); ?></h1>
<hr />
<div class="pull-right">
    <?= Url::link(['application/install'], '<i class="fa fa-tasks"></i> ' . __('Install'), ['class' => 'btn btn-primary']) ?>
</div>
<?php
$appTableItems = null;
foreach ($apps as $app) {
    if ($app->type !== 'app') {
        continue;
    }

    $route = $app->sys_name . '/index';
    $actions = \App::$View->show('macro/app_actions', ['controller' => $app->sys_name]);
    $appTableItems[] = [
        ['text' => $app->id],
        ['text' => ($app->disabled === 0 ? '<i class="fa fa-play"></i> ' : ' <i class="fa fa-pause"></i> ') .
            Url::link([$route], $app->getLocaleName()), 'html' => true, 'property' => ['class' => $app->disabled === 0 ? 'alert-success' : 'alert-danger']],
        ['text' => $app->disabled === 0 ? 'On' : 'Off'],
        ['text' => '<a target="_blank" href="' . \App::$Alias->scriptUrl . '/' . String::lowerCase($route) . '">' . $route . '</a>', 'html' => true],
        ['text' => Date::convertToDatetime($app->updated_at, Date::FORMAT_TO_HOUR)],
        ['text' => $actions, 'property' => ['class' => 'text-center'], 'html' => true]
    ];
}

?>


<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Application')],
            ['text' => __('Status')],
            ['text' => __('User interface')],
            ['text' => __('Activity')],
            ['text' => __('Actions')]
        ]
    ],
    'tbody' => [
        'items' => $appTableItems
    ]
]); ?>