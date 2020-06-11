<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Content\FormContentDelete $model */

$this->layout('_layouts/default', [
    'title' => __('Content delete')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Content delete') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Contents') => ['content/index'],
    __('Content delete')
]]) ?>

<?= $this->insert('content/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('id', ['disabled' => null]) ?>
<?= $form->fieldset()->text('title', ['disabled' => null]) ?>

<?= $form->button()->submit(__('Remove'), ['class' => 'btn btn-danger']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['content/index'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
