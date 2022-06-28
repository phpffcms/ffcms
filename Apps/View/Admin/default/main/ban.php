<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\Ban[] $records */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Main')
]);

?>

<?php $this->start('body') ?>

<h1><?= __('Ban') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Ban')
]]) ?>

<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs nav-fill'])
    ->menu(['text' => __('Spam'), 'link' => ['main/spam']])
    ->menu(['text' => __('Ban'), 'link' => ['main/ban']])
    ->display(); ?>

<div>
    <?= Url::a(['main/banupdate'], __('Add ban'), ['class' => 'btn btn-primary my-2']) ?>
</div>

<?php
$table = $this->table(['class' => 'table table-striped datatable'])
    ->head([
        ['text' => '#'],
        ['text' => __('IP / User')],
        ['text' => __('Read')],
        ['text' => __('Write')],
        ['text' => __('Expired')],
        ['text' => __('Actions')]
    ], ['class' => 'thead-light']);

foreach ($records as $record) {
    $btngrp = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm'])
        ->add('<i class="fas fa-pencil-alt"></i>', ['main/banupdate', [$record->id]], ['class' => 'btn btn-primary', 'html' => true])
        ->add('<i class="fas fa-trash-alt"></i>', ['main/bandel', [$record->id]], ['class' => 'btn btn-danger', 'data-bs-toggle' => 'tooltip', 'title' => __('Remove ban'), 'html' => true]);
    $table->row([
        ['text' => $record->id], 
        ['text' => ($record->ipv4 ?? '?') . ' / ' . ($record->user_id ? Url::a(['user/update', [$record->user_id]], 'User id' . $record->user_id) : '?'), 'html' => true],
        ['text' => $record->ban_read ? '<span class="badge badge-danger">-</span>' : '<span class="badge badge-success">+</span>', 'html' => true],
        ['text' => $record->ban_write ? '<span class="badge badge-danger">-</span>' : '<span class="badge badge-success">+</span>', 'html' => true],
        ['text' => $record->expired ?? '<span class="badge badge-danger">' . __('permanent') . '</span>', 'html' => true],
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