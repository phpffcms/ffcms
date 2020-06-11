<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Content\FormCategoryDelete $model */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Category delete')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Category delete') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Contents') => ['content/index'],
    __('Categories') => ['content/categories'],
    __('Category delete')
]]) ?>

<?= $this->insert('content/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('title', ['disabled' => null]) ?>
<?= $form->fieldset()->text('path', ['disabled' => null]) ?>

<?= $form->fieldset()->select('moveTo', ['options' => $model->categoryList(), 'optionsKey' => true], __('Select category acceptor of current category contents')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-danger']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['content/categories'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
