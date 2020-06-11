<?php

use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Admin\Contenttag\FormSettings $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Content tags')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Content tags') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Widgets') => ['widget/index'],
    __('Content tags') => ['contenttag/index'],
    __('Settings')
]]) ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('count', null, __('Set count of displayed tags in widget block'))?>
<?= $form->fieldset()->text('cache', null, __('Set default widget caching time. Set 0 to disable cache')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
