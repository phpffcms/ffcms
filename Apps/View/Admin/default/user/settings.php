<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $model \Apps\Model\Admin\UserSettings */

$this->title = __('Settings');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('user/index') => __('User list'),
    __('Settings')
];

?>

<?= $this->show('user/_tabs') ?>

<h1><?= $this->title ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']) ?>

<?= $form->field('registrationType', 'select', ['class' => 'form-control', 'options' => ['0' => __('Only invite'), '1' => __('Email approve'), '2' => __('Full opened')], 'optionsKey' => true]) ?>
<?= $form->field('captchaOnLogin', 'checkbox', null, __('Show captcha on login form?')) ?>
<?= $form->field('captchaOnRegister', 'checkbox', null, __('Show captcha on registration form?')) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>