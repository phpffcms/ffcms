<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Application\FormInstall $model */

$this->layout('_layouts/default', [
    'title' => __('Install app')
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Application install'); ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Install')
]]) ?>

<p><?= __('On this page you can install FFCMS application, using application system name. Please, type app.sys_name in form below.') ?></p>
<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('sysname', ['class' => 'form-control'], __('Specify your application system name for installation')) ?>

<?= $form->button()->submit(__('Try install'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-secondary', 'link' => ['application/index']]) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>