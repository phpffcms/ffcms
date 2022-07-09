<?php

/** @var Ffcms\Templex\Template\Template $this */
/** @var $useCaptcha bool */
/** @var \Apps\Model\Front\User\FormPasswordChange $model */

$this->layout('_layouts/default', [
    'title' => __('Recovery password')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Set / Recovery password') ?></h1>
<?= $this->insert('user/_menu/tabs') ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('password', null, __('Set new password for your account')) ?>
<?= $form->fieldset()->password('repassword', null, __('Repeat new password for your account')) ?>

<?= $this->insert('_core/form/fieldset/captcha', ['form' => $form]) ?>

<?= $form->button()->submit(__('Reset password'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>