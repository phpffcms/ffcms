<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;
use Ffcms\Core\Helper\Simplify;

/** @var \Apps\ActiveRecord\Content[]|\Illuminate\Support\Collection $records */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Content\FormContentPublish $model */

$this->layout('_layouts/default', [
    'title' => __('Content publish'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('content/index') => __('Contents'),
        __('Content publish')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('content/_tabs') ?>

<h1><?= __('Content publish') ?></h1>
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