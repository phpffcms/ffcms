<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Content\FormContentRestore $model */

$this->layout('_layouts/default', [
    'title' => __('Content restore')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Content restore') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Contents') => ['content/index'],
    __('Content restore')
]]) ?>

<?= $this->insert('content/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('id', ['disabled' => null]) ?>
<?= $form->fieldset()->text('title', ['disabled' => null]) ?>

<?= $form->button()->submit(__('Restore'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-secondary', 'link' => ['content/index', null, ['type' => 'trash']]]) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
