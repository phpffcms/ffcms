<?php

/** @var $this object */
/** @var $apps object */
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

$this->title = __('Applications');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Applications')
];
?>
<h1><?= __('List of applications'); ?></h1>
<hr />
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <?= Url::link(['application/install'], '<i class="glyphicon glyphicon-tasks"></i> ' . __('Install'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>
<?php
$appTableItems = null;
foreach ($apps as $app) {
    /** @var $app Apps\ActiveRecord\App */
    if ($app->type !== 'app') {
        continue;
    }

    $route = $app->sys_name . '/index';
    $icoStatus = null;
    $actions = \App::$View->render('native/macro/app_actions', ['controller' => $app->sys_name]);
    // set action icons based on app status
    if ((int)$app->disabled !== 0) {
        $icoStatus = ' <i class="glyphicon glyphicon-pause" style="color: #ff0000;"></i>';
    } elseif ($app->checkVersion() !== true) {
        $icoStatus = ' <i class="glyphicon glyphicon-exclamation-sign" style="color: #ffbd26;"></i>';
        $actions = Url::link(['application/update', $app->sys_name], '<i class="glyphicon glyphicon-wrench"></i>');
    } else {
        $icoStatus = ' <i class="glyphicon glyphicon-check" style="color: #008000;"></i>';
    }

    $appTableItems[] = [
        ['text' => $app->id . $icoStatus, 'html' => true, '!secure' => true],
        ['text' => Url::link([$route], $app->getLocaleName()), 'html' => true],
        ['text' => '<a target="_blank" href="' . \App::$Alias->scriptUrl . '/' . Str::lowerCase($route) . '">' . $route . '</a>', 'html' => true],
        ['text' => $app->version],
        ['text' => Date::convertToDatetime($app->updated_at, Date::FORMAT_TO_HOUR)],
        ['text' => $actions, 'property' => ['class' => 'text-center'], 'html' => true]
    ];
}

?>

<div class="table-responsive">
    <?= Table::display([
        'table' => ['class' => 'table table-bordered'],
        'thead' => [
            'titles' => [
                ['text' => '#'],
                ['text' => __('Application')],
                ['text' => __('User interface')],
                ['text' => __('Version')],
                ['text' => __('Activity')],
                ['text' => __('Actions')]
            ]
        ],
        'tbody' => [
            'items' => $appTableItems
        ]
    ]); ?>
</div>

