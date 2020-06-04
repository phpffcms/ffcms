<?php

use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Admin\Contenttag\FormSettings $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Content tags'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('contenttag/index') => __('Content tags'),
        __('Settings')
    ]
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Content tags') ?></h1>
<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('count', null, __('Set count of displayed tags in widget block'))?>
<?= $form->fieldset()->text('cache', null, __('Set default widget caching time. Set 0 to disable cache')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
