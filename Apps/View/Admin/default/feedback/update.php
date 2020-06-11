<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Feedback\FormUpdate $model */

$this->layout('_layouts/default', [
    'title' => __('Feedback update'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('feedback/index') => __('Feedback'),
        __('Update feedback')
    ]
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Feedback edit') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Feedback') => ['feedback/index'],
    __('Update feedback')
]]) ?>

<?= $this->insert('feedback/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('name', null, __('Author name for this item')) ?>
<?= $form->fieldset()->text('email', null, __('Author email for this item')) ?>
<?= $form->fieldset()->textarea('message', null, __('Message text')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>