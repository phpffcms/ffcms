<?php

use Ffcms\Templex\Url\Url;

/** @var \Apps\Model\Admin\Newcomment\FormSettings $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('New comments'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('newcomment/index') => __('New comments'),
        __('Settings')
    ]
]);
?>

<?php $this->start('body') ?>

<h1><?= __('New comments') ?></h1>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('snippet', null, __('Maximum length of comment text displayed in this widget'))?>
<?= $form->fieldset()->text('count', null, __('How many comments would be displayed in block?'))?>
<?= $form->fieldset()->text('cache', null, __('Widget default cache time in seconds. Set 0 to disable caching'))?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>