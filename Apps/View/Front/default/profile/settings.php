<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;
use Apps\ActiveRecord\ProfileField;

/** @var $model Apps\Model\Front\Profile\FormSettings */
/** @var $this object */

$this->title = __('Profile settings');

$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    Url::to('profile/show', \App::$User->identity()->getId()) => __('Profile'),
    __('Settings')
];

?>

<?= $this->show('profile/_settingsTab') ?>

<h1><?= __('Profile settings') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '', 'method' => 'post']) ?>

<?= $form->field('nick', 'text', ['class' => 'form-control'], __('Enter your nickname or real name')) ?>
<?= $form->field('sex', 'select', ['class' => 'form-control', 'options' => ['0' => __('Unknown'), '1' => __('Male'), '2' => __('Female')], 'optionsKey' => true], __('Choose your gender')) ?>
<?= $form->field('birthday', 'text', ['class' => 'form-control'], __('Enter your birthday date in d.m.Y format')) ?>
<?= $form->field('city', 'text', ['class' => 'form-control'], __('Enter the name of the city where you live')) ?>
<?= $form->field('hobby', 'text', ['class' => 'form-control'], __('Enter your hobbies in comma-separated format')) ?>
<?= $form->field('phone', 'text', ['class' => 'form-control'], __('Enter your phone number without spaces, if you want to make it public for other users')) ?>
<?= $form->field('url', 'text', ['class' => 'form-control'], __('If you have your own homepage - enter url there')) ?>
<?php
foreach (ProfileField::getAll() as $custom) {
    echo $form->field('custom_data.' . $custom->id, 'text', ['class' => 'form-control']);
}
?>

<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>