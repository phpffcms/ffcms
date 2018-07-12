<?php

use Ffcms\Templex\Url\Url;

/** @var $routes array */
/** @var $model \Apps\Model\Admin\Main\FormAddRoute */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Add route'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('main/routing') => __('Routing'),
        __('Add route')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('New route') ?></h1>
<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->select('type',
    [
        'options' => [
            'Alias' => __('Static (alias) route'),
            'Callback' => __('Dynamic (callback) route')
        ],
        'optionsKey' => true
    ],
    __('Specify type of defined rule')) ?>
<?= $form->fieldset()->radio('loader', ['options' => ['Front', 'Admin', 'Api']], __('Select loader type where be applied rule')) ?>
<?= $form->fieldset()->text('source', ['class' => 'form-control'], __('Define source path (for static alias) or class name (for dynamic rule) to use it for target query')) ?>
<?= $form->fieldset()->text('target', ['class' => 'form-control'], __('Define target path or class path for displayd item on source path')) ?>

<?= $form->button()->submit(__('Add new route'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['main/routing'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>
<?php $this->stop() ?>
