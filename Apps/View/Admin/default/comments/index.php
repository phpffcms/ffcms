<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Text;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\CommentPost[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */

$this->layout('_layouts/default', [
    'title' => __('Comments list')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Comments list') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Widgets') => ['widget/index'],
    __('Comments')
]]) ?>

<?= $this->insert('comments/_tabs'); ?>

<?php
if (!$records || $records->count() < 1) {
    echo $this->bootstrap()->alert('warning', __('Comments is not founded'));
    $this->stop();
    return;
}
$items = [];
$moderateIsFound = false;
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Comment')],
        ['text' => __('Answers')],
        ['text' => __('Author')],
        ['text' => __('Page')],
        ['text' => __('Date')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']],
    ], ['class' => 'thead-dark']);

foreach ($records as $item) {
    $message = Text::cut(\App::$Security->strip_tags($item->message), 0, 75);

    $moderate = (bool)$item->moderate;
    // if even one moderate item is found - change global flag to true
    if ($moderate) {
        $moderateIsFound = true;
    }

    $btngrp = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm']);

    if ($moderate) {
        $btngrp->add('<i class="fas fa-eye-slash" style="color: #aa2222;"></i>', ['comments/display', ['comment', $item->id]], ['class' => 'btn btn-light', 'data-toggle' => 'tooltip', 'title' => __('Comment is hidden'), 'html' => true]);
    } else {
        $btngrp->add('<i class="fas fa-eye" style="color: #1a8007"></i>', ['comments/display', ['comment', $item->id]], ['class' => 'btn btn-light', 'data-toggle' => 'tooltip', 'title' => __('Comment is public'), 'html' => true]);
    }

    $btngrp->add('<i class="fas fa-list"></i>', ['comments/read', [$item->id]], ['class' => 'btn btn-primary', 'html' => true])
        ->add('<i class="fas fa-trash-alt"></i>', ['comments/delete', ['comment', $item->id]], ['class' => 'btn btn-danger', 'html' => true]);

    $table->row([
        ['text' => $item->id],
        ['text' => Url::a(['comments/read', [$item->id]], $message), 'html' => true],
        ['text' => '<span class="badge badge-light">' . $item->getAnswerCount() . '</span>', 'html' => true],
        ['text' => Simplify::parseUserLink((int)$item->user_id, $item->guest_name, 'user/update'), 'html' => true],
        ['text' => '<a href="'.Url::stringUrl($item->app_name . '/comments/' . $item->app_relation_id).'" target="_blank">' . $item->app_name . '/' . $item->app_relation_id . '</a>', 'html' => true],
        ['text' => Date::convertToDatetime($item->created_at, Date::FORMAT_TO_HOUR)],
        ['text' => $btngrp->display(), 'html' => true, 'properties' => ['class' => 'text-center']],
        'properties' => ['class' => 'checbox-row']
    ]);
}

$table->selectize(0, 'selected');
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= $this->javascript()->submitSelectizeTable('input[name="selected[]"]', 'selected', __('Delete selected'), ['comments/delete', ['comment']], ['class' => 'btn btn-danger']) ?>
<?php if ($moderateIsFound) {
    echo $this->javascript()->submitSelectizeTable('input[name="selected[]"]', 'selected', __('Publish'), ['comments/publish', ['comment']], ['class' => 'btn btn-warning']);
} ?>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>


<?php $this->stop() ?>