<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\User\FormUserGroupUpdate $model */

$this->layout('_layouts/default', [
    'title' => __('Manage role')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Manage role') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Users') => ['user/index'],
    __('Role list') => ['user/rolelist'],
    __('Manage role')
]]) ?>

<?= $this->insert('user/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('name', null, __('Set role title')) ?>
<?= $form->fieldset()->text('color', null, __('Set role display color. Use standard RGB hex color scheme')) ?>
<?= $form->fieldset()->checkboxes('permissions', ['options' => $model->getAllPermissions()]) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['user/rolelist'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?= $this->stop() ?>
