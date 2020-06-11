<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Comments\FormSettings $model */

$this->layout('_layouts/default', [
    'title' => __('Comments settings')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Comments settings') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Widgets') => ['widget/index'],
    __('Comments') => ['comments/index'],
    __('Settings')
]]) ?>

<?= $this->insert('comments/_tabs'); ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('perPage', null, __('Comments count to display per one page')) ?>
<?= $form->fieldset()->text('delay', null, __('Delay between 2 comment posts or answers from one user (in seconds)')) ?>
<?= $form->fieldset()->text('minLength', null, __('Minimal comment length to be valid')) ?>
<?= $form->fieldset()->text('maxLength', null, __('Maximum comment length to be valid')) ?>

<?= $form->fieldset()->boolean('onlyLocale', null, __('Show only localized comments and answers for current user locale?')) ?>
<?= $form->fieldset()->boolean('guestAdd', null, __('Allow add comments for not authorized users?')) ?>
<?= $form->fieldset()->boolean('guestModerate', null, __('Set pre-moderation for guest comments?'))?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>