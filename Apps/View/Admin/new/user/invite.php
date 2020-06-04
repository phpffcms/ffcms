<?php

use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Admin\User\FormInviteSend $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Send invite'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('user/index') => __('User list'),
        __('Send invite')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('user/_tabs') ?>
<h1><?= __('Send invite') ?></h1>
<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('email', null, __('Specify user email')) ?>
<?= $form->button()->submit(__('Send'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['user/index'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
