<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Comments\FormCommentDelete $model */
/** @var string $type */

$this->layout('_layouts/default', [
    'title' => __('Delete comments')
]);
$records = $model->getRecord();
?>

<?php $this->start('body') ?>

<h1><?= __('Delete comments and answers') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Widgets') => ['widget/index'],
    __('Comments') => ['comments/index'],
    __('Delete comments and answers')
]]) ?>

<?= $this->insert('comments/_tabs'); ?>

<?= __('Are you sure to delete this comments or answers?') ?>

<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Message')],
        ['text' => __('Author')],
        ['text' => __('Date')]
    ]);
foreach ($records as $item) {
    $table->row([
        ['text' => $item->id],
        ['text' => Str::sub(\App::$Security->strip_tags($item->message), 0, 50)],
        ['text' => Url::a(['user/update', [$item->user_id]], Simplify::parseUserName($item->user_id, $item->guest_name)), 'html' => true],
        ['text' => Date::convertToDatetime($item->created_at, Date::FORMAT_TO_HOUR)]
    ]);
}

?>

<div class="table-responsive"><?= $table->display() ?></div>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->button()->submit(__('Delete'), ['class' => 'btn btn-danger'])?>

<?= $form->stop() ?>

<?php $this->stop() ?>
