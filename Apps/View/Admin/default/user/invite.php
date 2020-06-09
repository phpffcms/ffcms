<?php

use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Admin\User\FormInviteSend $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Send invite')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Send invite') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Users') => ['user/index'],
    __('Invitation list') => ['user/invitelist'],
    __('Send invite')
]]) ?>

<?= $this->insert('user/_tabs') ?>

<?php $form = $this->form($model, ['class' => 'py-2']) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('email', null, __('Specify user email')) ?>
<?= $form->button()->submit(__('Send'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['user/index'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
