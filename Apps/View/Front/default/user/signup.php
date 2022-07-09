<?php

/** @var $notify array */
/** @var $useCaptcha bool */
/** @var $model \Apps\Model\Front\User\FormRegister */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Sign up')
])
?>

<?php $this->start('body') ?>

<h1><?= __('Sign up'); ?></h1>
<?= $this->insert('user/_menu/tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?php // $this->insert('user/_menu/_social_panel') ?>

<?= $form->fieldset()->text('email', null, __('Enter your e-mail for an account')); ?>
<?= $form->fieldset()->password('password', null, __('Enter a password for your account. It should be: longer than 6 chars, contains chars & numbers, contains at least 1 uppercase symbol')); ?>
<?= $form->fieldset()->password('repassword', null, __('Repeat your password to be sure it correct')); ?>

<?= $this->insert('_core/form/fieldset/captcha', ['form' => $form]) ?>

<?= $form->button()->submit(__('Register!'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?= $this->stop() ?>
