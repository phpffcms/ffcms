<?php

/** @var $this object */
/** @var $apps object */
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Type\Str;
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
    $actions = \App::$View->render('macro/app_actions', ['controller' => $app->sys_name]);
    $icoStatus = null;
    if ((int)$app->disabled === 0) {
        $icoStatus = ' <spal class="label label-success"><i class="fa fa-play"></i></span>';
    } else {
        $icoStatus = ' <span class="label label-danger"><i class="fa fa-pause"></i></span>';
    }

    $appTableItems[] = [
        ['text' => $app->id . $icoStatus, 'html' => true, '!secure' => true],
        ['text' => Url::link([$route], $app->getLocaleName()), 'html' => true],
        ['text' => '<a target="_blank" href="' . \App::$Alias->scriptUrl . '/' . Str::lowerCase($route) . '">' . $route . '</a>', 'html' => true],
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
            ['text' => __('User interface')],
            ['text' => __('Activity')],
            ['text' => __('Actions')]
        ]
    ],
    'tbody' => [
        'items' => $appTableItems
    ]
]); ?>