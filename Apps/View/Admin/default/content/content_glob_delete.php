<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this*/
/** @var Apps\Model\Admin\Content\FormContentGlobDelete $model */

$this->layout('_layouts/default', [
    'title' => __('Content global delete'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('content/index') => __('Contents'),
        __('Content delete')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('content/_tabs') ?>
<h1><?= __('Content delete') ?></h1>

<p><?= __('Are you sure to delete all this content items?'); ?></p>
<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Title')],
        ['text' => __('Date')]
    ]);


$items = [];
foreach ($model->data as $item) {
    $table->row([
        ['type' => 'text', 'text' => $item['id']],
        ['type' => 'text', 'text' => $item['title']],
        ['type' => 'text', 'text' => $item['date']]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= $form->button()->submit(__('Delete all'), ['class' => 'btn btn-danger']); ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['content/index'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
