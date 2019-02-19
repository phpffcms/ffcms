<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\App[] $widgets */

$this->layout('_layouts/default', [
    'title' => __('Widgets'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        __('Widgets')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Widgets list'); ?></h1>
<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Widget')],
        ['text' => __('Version')],
        ['text' => __('Activity')],
        ['text' => __('Actions')]
    ]);


foreach ($widgets as $widget) {
    $controller = Str::lowerCase($widget->sys_name);
    $route = $controller . '/index';
    $icoStatus = null;
    $actions = $this->fetch('widget/_actions', ['controller' => $controller]);
    if ((bool)$widget->disabled) {
        $icoStatus = ' <i class="fas fa-pause" style="color: #ff0000;"></i>';
    } elseif (!$widget->checkVersion()) {
        $icoStatus = ' <i class="fas fa-exclamation-circle" style="color: #ffbd26;"></i>';
        $actions = Url::a(['widget/update', [$controller]], '<i class="fas fa-wrench"></i>', ['html' => true]);
    } else {
        $icoStatus = ' <i class="fas fa-check" style="color: #008000;"></i>';
    }

    $table->row([
        ['text' => $widget->id . $icoStatus, 'html' => true],
        ['text' => Url::a([$route], $widget->getLocaleName()), 'html' => true],
        ['text' => $widget->version],
        ['text' => Date::convertToDatetime($widget->updated_at, Date::FORMAT_TO_HOUR)],
        ['text' => $actions, 'html' => true]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= Url::a(['widget/install'], '<i class="fas fa-tasks"></i> ' . __('Install'), ['class' => 'btn btn-primary', 'html' => true]) ?>
<?php $this->stop() ?>