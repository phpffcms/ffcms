<?php

/** @var Ffcms\Templex\Template\Template $this */
/** @var $useCaptcha bool */
/** @var \Apps\Model\Front\User\FormLogin $model */
/** @var string $redirect */

$this->layout('_layouts/default', [
    'title' => __('Log In')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Log In') ?></h1>
<?= $this->insert('user/_menu/tabs') ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('email', null, __('Input your email')) ?>
<?= $form->fieldset()->password('password', null, __('Input you password')) ?>

<?php if ($useCaptcha): ?>
    <?= $this->insert('_core/form/fieldset/captcha', ['form' => $form]) ?>
<?php endif; ?>

<?= $form->button()->submit(__('Do Login'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>