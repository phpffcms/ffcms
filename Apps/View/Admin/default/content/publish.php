<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\Content[]|\Illuminate\Support\Collection $records */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Content\FormContentPublish $model */

$this->layout('_layouts/default', [
    'title' => __('Content publish')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Content publish') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Contents') => ['content/index'],
    __('Content publish')
]]) ?>

<?= $this->insert('content/_tabs') ?>

<p><?= __('Are you sure to make this item public?') ?></p>
<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Title')],
        ['text' => __('Author')],
        ['text' => __('Date')]
    ]);

foreach ($records as $record) {
    $table->row([
        ['text' => $record->id],
        ['text' => $record->getLocaled('title')],
        ['text' => Simplify::parseUserLink($record->author_id), 'html' => true],
        ['text' => Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR)]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->button()->submit(__('Publish'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-secondary', 'link' => ['content/index']]) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>