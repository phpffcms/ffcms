<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\App $app */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Application\FormTurn $model */

$this->layout('_layouts/default', [
    'title' => __('Turn on/off'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        __('Turn on/off')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Application turn on/off') ?></h1>
<div class="table-responsive">
<?= $this->table(['class' => 'table'])
    ->head([
        ['text' => __('Param')],
        ['text' => __('Value')]
    ])->body([
        [['text' => __('Name')], ['text' => $app->getLocaleName()]],
        [['text' => __('System name')], ['text' => $app->sys_name]],
        [['text' => __('Last update')], ['text' => Date::convertToDatetime($app->updated_at, Date::FORMAT_TO_SECONDS)]],
        [['text' => __('Status')], ['text' => !(bool)$app->disabled ? 'On' : 'Off'], 'properties' => ['class' =>  !(bool)$app->disabled ? 'alert-success' : 'alert-danger']]
    ])->display() ?>
</div>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->button()->submit(__('Switch'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['application/index'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
