<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Comments\FormSettings $model */

$this->layout('_layouts/default', [
    'title' => __('Comments settings'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('widget/index') => __('Widgets'),
        Url::to('comments/index') => __('Comments'),
        __('Settings')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('comments/_tabs') ?>

<h1><?= __('Comments settings') ?></h1>

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