<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var $model Apps\Model\Admin\Application\FormUpdate */

$this->layout('_layouts/default', [
    'title' => __('Update widget'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('widget/index') => __('Widgets'),
        __('Update')
    ]
]);
?>
<?php $this->start('body') ?>
<h1><?= __('Update widget'); ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Widgets') => ['widget/index'],
    __('Update')
]]) ?>

<?= $this->table(['class' => 'table'])
    ->head([
        ['text' => __('Widget name')],
        ['text' => __('Script version')],
        ['text' => __('DB version')],
        ['text' => __('Last changes')]
    ])->body([
        [
            ['text' => $model->name],
            ['text' => $model->scriptVersion],
            ['text' => $model->dbVersion],
            ['text' => $model->date]
        ]
    ])->display() ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>
<?= $form->button()->submit(__('Try update'), ['class' => 'btn btn-primary']) ?>
<?= $form->stop(false) ?>

<?php $this->stop() ?>
