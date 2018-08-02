<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Front\User\FormRecovery $model */

$this->layout('_layouts/default', [
    'title' => __('Recovery')
])

?>
<?php $this->start('body') ?>

<h1><?= __('Recovery form') ?></h1>
<?= $this->insert('user/_menu/tabs') ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('email', null, __('Input your account email')); ?>
<?= $this->insert('_core/form/fieldset/captcha', ['form' => $form]) ?>

<?= $form->button()->submit(__('Make recovery'), ['class' => 'btn btn-primary']) ?>
<?= $form->stop(); ?>

<?= $this->stop() ?>
