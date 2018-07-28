<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\App[] $apps */

$this->layout('_layouts/default', [
    'title' => __('Applications'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        __('Applications')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('List of applications'); ?></h1>do
<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Application')],
        ['text' => __('User interface')],
        ['text' => __('Version')],
        ['text' => __('Activity')],
        ['text' => __('Actions')]
    ]);

foreach ($apps as $app) {
    if ($app->type !== 'app') {
        continue;
    }
    $controller = Str::lowerCase($app->sys_name);
    $route = $controller . '/index';
    $icoStatus = null;
    $actions = $this->fetch('application/_actions', ['controller' => $controller]);
    // set action icons based on app status
    if ((bool)$app->disabled) {
        $icoStatus = ' <i class="fa fa-pause" style="color: #ff0000;"></i>';
    } elseif (!$app->checkVersion()) {
        $icoStatus = ' <i class="fa fa-exclamation-circle" style="color: #ffbd26;"></i>';
        $actions = Url::a(['application/update', [$controller]], '<i class="fa fa-wrench"></i>', ['html' => true]);
    } else {
        $icoStatus = ' <i class="fa fa-check" style="color: #008000;"></i>';
    }

    $table->row([
        ['text' => $app->id . $icoStatus, 'html' => true],
        ['text' => Url::a([$route], $app->getLocaleName()), 'html' => true],
        ['text' => '<a target="_blank" href="' . \App::$Alias->scriptUrl . '/' . Str::lowerCase($route) . '">' . $route . '</a>', 'html' => true],
        ['text' => $app->version],
        ['text' => Date::convertToDatetime($app->updated_at, Date::FORMAT_TO_HOUR)],
        ['text' => $actions, 'html' => true]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>
<?= Url::a(['application/install'], '<i class="fa fa-tasks"></i> ' . __('Install app'), ['class' => 'btn btn-primary', 'html' => true]) ?>
<?php $this->stop() ?>