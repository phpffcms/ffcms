<?php

use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Front\Profile\FormSettings $model */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\Profile $profile */

$this->layout('_layouts/default', [
    'title' => __('Profile edit')
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('profile/_tabs') ?>
<h1><?= __('Edit user profile') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Profile list') => ['profile/index'],
    __('Profile edit')
]]) ?>

<?= $this->insert('profile/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('nick') ?>
<?= $form->fieldset()->select('sex', ['options' => ['0' => __('Unknown'), '1' => __('Male'), '2' => __('Female')], 'optionsKey' => true]) ?>
<?= $form->fieldset()->text('birthday', null, __('Birthday date in d.m.Y format')) ?>
<?= $form->fieldset()->text('city') ?>
<?= $form->fieldset()->text('hobby') ?>
<?= $form->fieldset()->text('phone') ?>
<?= $form->fieldset()->text('url') ?>

<div class="row mb-5">
    <div class="col-md-3">
        <div class="text-right"><strong><?= __('Account data') ?></strong></div>
    </div>
    <div class="col-md-9">
        <?= Url::a(['user/update', [$profile->user->id]], __('Edit account data')); ?>
    </div>
</div>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['profile/index'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
