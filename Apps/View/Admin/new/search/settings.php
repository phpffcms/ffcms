<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var $model Apps\Model\Admin\Search\FormSettings */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Search settings'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('search/index') => __('Search'),
        __('Settings')
    ]
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Search settings') ?></h1>
<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('minLength', null, __('Set minimum user search query length. We are strongly recommend set this value more then 2.')) ?>
<?= $form->fieldset()->text('itemPerApp', null, __('How many items would be shown in result for each search instance?')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
