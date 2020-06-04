<?php

use Ffcms\Templex\Url\Url;

/** @var $model \Apps\Model\Admin\Main\EntityDeleteRoute */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Deleting route'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('main/routing') => __('Routing'),
        __('Deleting route')
    ]
]);

?>

<?php $this->start('body') ?>
<h1><?= __('Deleting route') ?></h1>
<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('type', ['disabled' => true]) ?>
<?= $form->fieldset()->text('loader', ['disabled' => true]) ?>
<?= $form->fieldset()->text('source', ['disabled' => true]) ?>

<?= $form->button()->submit(__('Delete this route'), ['class' => 'btn btn-danger']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-secondary', 'link' => ['main/routing']]) ?>

<?= $form->stop() ?>
<?php $this->stop() ?>
