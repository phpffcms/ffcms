<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Application\FormInstall $model */

$this->layout('_layouts/default', [
    'title' => __('Install widget'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('widget/index') => __('Widgets'),
        __('Install')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Widget install'); ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Widgets') => ['widget/index'],
    __('Install')
]]) ?>

<p><?= __('On this page you can install FFCMS widget, using widget system name. Please, type widget.sys_name in form below.') ?></p>
<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('sysname', null, __('Specify your widget system name for installation')) ?>

<?= $form->button()->submit(__('Try install'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-secondary', 'link' => ['widget/index']]) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>