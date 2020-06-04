<?php

use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Admin\Profile\FormSettings $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Profile settings'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('profile/index') => __('Profile'),
        __('Settings')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('profile/_tabs') ?>
<h1><?= __('Profile settings') ?></h1>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('usersOnPage', null, __('How many users will be displayed per one list page?')) ?>
<?= $form->fieldset()->boolean('guestView', null, __('Allow guests view user profiles?')) ?>
<?= $form->fieldset()->text('wallPostOnPage', null,  __('How many wall posts must be displayed in one page of profile?')) ?>
<?= $form->fieldset()->text('wallPostOnFeed', null, __('How many wall posts must be displayed on one page of feed list?')) ?>
<?= $form->fieldset()->text('delayBetweenPost', null, __('Delay between 2 posts on wall from one user in seconds')) ?>
<?= $form->fieldset()->boolean('rating', null, __('Enable user rating system?')) ?>
<?= $form->fieldset()->text('ratingDelay', null, __('Delay in seconds between repeat change of rating from one user(model: one to one)')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
