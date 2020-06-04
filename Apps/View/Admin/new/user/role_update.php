<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\User\FormUserGroupUpdate $model */

$this->layout('_layouts/default', [
    'title' => __('Manage role'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        __('Manage role')
    ]
]);
?>

<?php $this->start('body') ?>
<?= $this->insert('user/_tabs') ?>

<h1><?= __('Manage role') ?></h1>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('name', null, __('Set role title')) ?>
<?= $form->fieldset()->text('color', null, __('Set role display color. Use standard RGB hex color scheme')) ?>
<?= $form->fieldset()->checkboxes('permissions', ['options' => $model->getAllPermissions()]) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['user/rolelist'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?= $this->stop() ?>
