<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Application\FormUpdate $model */

$this->layout('_layouts/default', [
    'title' => __('Update app')
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Update app'); ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Update')
]]) ?>

<?= $this->table(['class' => 'table'])
    ->head([
        ['text' => __('App name')],
        ['text' => __('Script version')],
        ['text' => __('DB version')],
        ['text' => __('Last changes')]
    ])->body([
        [
            ['text' => $model->name],
            ['text' => $model->scriptVersion],
            ['text' => $model->dbVersion],
            ['text' => $model->date]
        ]
    ])->display() ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->button()->submit(__('Try update'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop(false) ?>

<?php $this->stop() ?>
