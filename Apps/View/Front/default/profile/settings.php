<?php

use Apps\ActiveRecord\ProfileField;
use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Front\Profile\FormSettings $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Profile settings'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        Url::to('profile/show', [\App::$User->identity()->getId()]) => __('Profile'),
        __('Settings')
    ]
]);
?>

<?php $this->start('body') ?>

<?php $this->insert('profile/menus/settings') ?>

<h1><?= __('Profile settings') ?></h1>
<hr />

<?php $form = $this->form($model); ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('name', null, __('Enter your full name identity')) ?>
<?= $form->fieldset()->select('sex', ['options' => ['0' => __('Unknown'), '1' => __('Male'), '2' => __('Female')], 'optionsKey' => true], __('Choose your gender')) ?>
<?= $form->fieldset()->text('birthday', null, __('Enter your birthday date in d.m.Y format')) ?>
<?= $form->fieldset()->text('city', null, __('Enter the name of the city where you live')) ?>
<?= $form->fieldset()->text('hobby', null, __('Enter your hobbies in comma-separated format')) ?>
<?= $form->fieldset()->text('phone', null, __('Enter your phone number without spaces, if you want to make it public for other users')) ?>
<?= $form->fieldset()->text('url', null, __('If you have your own homepage - enter url there')) ?>

<?php
foreach (ProfileField::all() as $custom) {
    echo $form->fieldset()->text('custom_data.' . $custom->id);
}
?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
