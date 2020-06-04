<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\User\FormUserSettings $model */

$this->layout('_layouts/default', [
    'title' => __('Settings'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('user/index') => __('User list'),
        __('Settings')
    ]
]);
?>

<?php $this->start('body') ?>
<?= $this->insert('user/_tabs') ?>

<h1><?= __('Settings') ?></h1>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->select('registrationType', ['options' => ['0' => __('Only invite'), '1' => __('Email approve'), '2' => __('Full opened')], 'optionsKey' => true]) ?>
<?= $form->fieldset()->boolean('captchaOnLogin', null, __('Show captcha on login form?')) ?>
<?= $form->fieldset()->boolean('captchaOnRegister', null, __('Show captcha on registration form?')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
