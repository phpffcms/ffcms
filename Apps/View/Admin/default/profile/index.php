<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\Profile[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Profile list'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        __('Profile')
    ]
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Profile list') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Profile')
]]) ?>

<?= $this->insert('profile/_tabs') ?>

<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => 'email'],
        ['text' => __('Nickname')],
        ['text' => __('Birthday')],
        ['text' => __('Rating')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ], ['class' => 'thead-light']);

foreach ($records as $profile) {
    $table->row([
        ['text' => $profile->id],
        ['text' => $profile->user->email],
        ['text' => $profile->nick],
        ['text' => Str::startsWith('0000-', $profile->birthday) ? __('None') : Date::convertToDatetime($profile->birthday)],
        ['text' => ($profile->rating > 0 ? '+' : null) . $profile->rating],
        ['text' => $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm'])
            ->add('<i class="fas fa-pencil-alt"></i>', ['profile/update', [$profile->id]], ['html' => true, 'class' => 'btn btn-primary'])
            ->add('<i class="fas fa-trash-alt"></i>', ['user/delete', [$profile->user->id]], ['html' => true, 'class' => 'btn btn-danger'])
            ->display(), 'html' => true, 'properties' => ['class' => 'text-center']]
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