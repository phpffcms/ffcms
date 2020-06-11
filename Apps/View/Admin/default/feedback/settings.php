<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Feedback\FormSettings $model */

$this->layout('_layouts/default', [
    'title' => __('Settings')
]);

?>

<?php $this->start('body') ?>

<h1><?= __('Feedback settings') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Feedback') => ['feedback/index'],
    __('Settings')
]]) ?>

<?= $this->insert('feedback/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->boolean('guestAdd', null, __('Allow not authorized users add feedback requests?')) ?>
<?= $form->fieldset()->boolean('useCaptcha', null, __('Use captcha on feedback form to prevent spam?')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
