<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Front\Feedback\FormFeedbackAdd $model */
/** @var bool $useCaptcha */

$this->layout('_layouts/default', [
    'title' => __('Feedback'),
    'breadcrumbs' => [
        Url::to('/') => __('Home'),
        __('Feedback')
    ]
]);
?>
<?php $this->start('body') ?>
<h1><?= __('Feedback') ?></h1>
<?php if (\App::$User->isAuth()): ?>
    <?= $this->insert('feedback/_authTabs') ?>
<?php else: ?>
    <hr />
<?php endif; ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('name', null, __('Enter your name, used in feedback emails')) ?>
<?= $form->fieldset()->text('email', null, __('Enter the email to contact with you')) ?>
<?= $form->fieldset()->textarea('message', null, __('Enter your feedback request text. Please, dont use HTML or other codes.')) ?>

<?php if ($useCaptcha): ?>
    <?= $this->insert('_form/fieldset/captcha', ['form' => $form]) ?>
<?php endif; ?>

<?= $form->button()->submit(__('Send'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
