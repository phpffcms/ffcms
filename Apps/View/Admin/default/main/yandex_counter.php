<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Main\FormYandexCounter $model */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Yandex.Metrika - counter'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        __('Yandex metrika - step 3')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Yandex.metrika - choose counter') ?></h1>
<p><?= __('Now you should choose counter statistics id to display on main page') ?>.</p>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->select('counter', ['options' => $model->getCounters(), 'optionsKey' => true]) ?>
<?= $form->button()->submit(__('Select and connect'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
