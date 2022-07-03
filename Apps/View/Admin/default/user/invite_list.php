<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\Invite[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */
/** @var array $configs */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Invitation list')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Invitation list') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Users') => ['user/index'],
    __('Invitation list')
]]) ?>

<?= $this->insert('user/_tabs') ?>

<?php if ($configs['registrationType'] !== 0) {
    echo $this->bootstrap()->alert('danger', __('Invite system is disabled. Registration is public'));
} ?>
<div>
    <?= Url::a(['user/invite'], __('Send invite'), ['class' => 'btn btn-info my-2']) ?>
</div>

<?php if ($records->count() < 1) {
    echo $this->bootstrap()->alert('warning', __('No invites recently send'));
    $this->stop();
    return;
} ?>

<?php
$table = $this->table(['class' => 'table table-striped', 'data-toggle' => 'datatable', 'data-column-defs' => '[{"targets": [2,4], "orderable": false}]'])
    ->head([
        ['text' => '#'],
        ['text' => __('Email')],
        ['text' => __('Valid')],
        ['text' => __('Send date')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ], ['class' => 'thead-light']);


foreach ($records as $invite) {
    $time = Date::convertToTimestamp($invite->created_at);

    $btngrp = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm'])
        ->add('<i class="fas fa-trash"></i>', ['user/invitedelete', [$invite->id]], ['html' => true, 'class' => 'btn btn-danger']);

    $table->row([
        ['text' => $invite->id],
        ['text' => $invite->email],
        ['text' => (time() - $time < \Apps\ActiveRecord\Invite::TOKEN_VALID_TIME ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-minus text-danger"></i>'), 'html' => true],
        ['text' => Date::convertToDatetime($invite->created_at, Date::FORMAT_TO_DAY)],
        ['text' => $btngrp->display(), 'html' => true, 'properties' => ['class' => 'text-center']]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<?php $this->stop() ?>