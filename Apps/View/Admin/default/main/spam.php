<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\Spam[] $records */
/** @var array $stats */
/** @var boolean $tokenActive */
/** @var array $yandexCfg */
/** @var array|null $visits */
/** @var array|null $sources */

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Main')
]);

?>

<?php $this->start('body') ?>

<h1><?= __('Spam threats') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Spam')
]]) ?>

<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs nav-fill'])
    ->menu(['text' => __('Spam'), 'link' => ['main/spam']])
    ->menu(['text' => __('Ban'), 'link' => ['main/ban']])
    ->display(); ?>

<?php
$table = $this->table(['class' => 'table table-striped datatable'])
    ->head([
        ['text' => '#'],
        ['text' => 'IP'],
        ['text' => 'User'],
        ['text' => __('Count')],
        ['text' => __('Activity')],
        ['text' => __('Actions')]
    ], ['class' => 'thead-dark']);

foreach ($records as $record) {
    $btngrp = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm'])
        ->add('<i class="fas fa-ban"></i>', ['main/banupdate', [], ['ip' => $record->ipv4, 'user' => $record->user_id]], ['class' => 'btn btn-danger', 'data-toggle' => 'tooltip', 'title' => __('Ban user / ip'), 'html' => true]);
    $table->row([
        ['text' => $record->id],
        ['text' => $record->ipv4],
        ['text' => ($record->user_id ? Url::a(['user/update', [$record->user_id]], 'User id' . $record->user_id) : '?'), 'html' => true],
        ['text' => $record->counter],
        ['text' => Date::convertToDatetime($record->updated_at, Date::FORMAT_TO_HOUR)],
        ['text' => $btngrp->display(), 'html' => true, 'properties' => ['class' => 'text-center']]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display()
?>


<?php $this->stop() ?>