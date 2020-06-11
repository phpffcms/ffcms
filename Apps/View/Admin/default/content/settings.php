<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var $model Apps\Model\Admin\Content\FormSettings */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Settings')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Content settings') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Contents') => ['content/index'],
    __('Settings')
]]) ?>

<?= $this->insert('content/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('itemPerCategory', null, __('Count of content items per each page of category')) ?>
<?= $form->fieldset()->boolean('userAdd', null, __('Allow users add content pages?')) ?>
<?= $form->fieldset()->boolean('multiCategories', null, __('Display content from child categories?')) ?>
<?= $form->fieldset()->text('galleryResize', null, __('Specify maximum size of image in gallery in px')) ?>
<?= $form->fieldset()->text('gallerySize', null, __('Specify maximum image size in gallery in kb. Example: 500 = 0,5 mb')) ?>
<?= $form->fieldset()->boolean('rss', null, __('Allow use RSS display for categories where this is enabled?')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
