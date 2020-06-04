<?php

use Ffcms\Templex\Url\Url;

/** @var \Apps\Model\Admin\Newcontent\FormSettings $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('New content'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('newcontent/index') => __('New content'),
        __('Settings')
    ]
]);
?>

<?php $this->start('body') ?>

<h1><?= __('New content') ?></h1>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->multiselect('categories', ['options' => $model->getCategories(), 'optionsKey' => true, 'size' => 4], __('Select categories of wich content will be selected')) ?>
<?= $form->fieldset()->text('count', null, __('How many content items would be displayed in block?'))?>
<?= $form->fieldset()->text('cache', null, __('Widget default cache time in seconds. Set 0 to disable caching.'))?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
