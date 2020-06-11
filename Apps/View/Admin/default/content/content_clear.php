<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Content\FormContentClear $model */

$this->layout('_layouts/default', [
    'title' => __('Cleanup trash')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Cleanup trash') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Contents') => ['content/index'],
    __('Cleanup trash')
]]) ?>

<?= $this->insert('content/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('count', ['disabled' => null], __('Count of content items to total remove')) ?>

<?= $form->button()->submit(__('Total remove'), ['class' => 'btn btn-danger']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-secondary', 'link' => ['content/index', null, ['type' => 'trash']]]) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
