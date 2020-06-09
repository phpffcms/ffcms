<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\User\FormUserSettings $model */

$this->layout('_layouts/default', [
    'title' => __('Settings')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Settings') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Users') => ['user/index'],
    __('Settings')
]]) ?>

<?= $this->insert('user/_tabs') ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->select('registrationType', ['options' => ['0' => __('Only invite'), '1' => __('Email approve'), '2' => __('Full opened')], 'optionsKey' => true]) ?>
<?= $form->fieldset()->boolean('captchaOnLogin', null, __('Show captcha on login form?')) ?>
<?= $form->fieldset()->boolean('captchaOnRegister', null, __('Show captcha on registration form?')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
