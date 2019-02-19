<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Text;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\CommentAnswer[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */

$this->layout('_layouts/default', [
    'title' => __('Answers list'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('widget/index') => __('Widgets'),
        Url::to('comments/index') => __('Comments'),
        __('Answers')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('comments/_tabs') ?>

<h1><?= __('Answers list') ?></h1>
<?php
if (!$records || $records->count() < 1) {
    echo $this->bootstrap()->alert('warning', __('Answers is not founded'));
    $this->stop();
    return;
}
$items = [];
$moderateIsFound = false;
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Answer')],
        ['text' => __('Author')],
        ['text' => __('Date')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ]);

foreach ($records as $item) {
    /** @var \Apps\ActiveRecord\CommentAnswer $item */
    $message = Text::cut(\App::$Security->strip_tags($item->message), 0, 75);
    $moderate = (bool)$item->moderate;
    if ($moderate) {
        $moderateIsFound = true;
    }

    $btngrp = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm']);
    if ($moderate) {
        $btngrp->add('<i class="fas fa-eye-slash" style="color: #aa2222;"></i>', ['comments/display', ['answer', $item->id]], ['class' => 'btn btn-light', 'data-toggle' => 'tooltip', 'title' => __('Answer is hidden'), 'html' => true]);
    } else {
        $btngrp->add('<i class="fas fa-eye" style="color: #1a8007"></i>', ['comments/display', ['answer', $item->id]], ['class' => 'btn btn-light', 'data-toggle' => 'tooltip', 'title' => __('Answer is public'), 'html' => true]);
    }
    $btngrp->add('<i class="fas fa-list"></i>', ['comments/read', [$item->post->id]], ['class' => 'btn btn-primary', 'html' => true])
        ->add('<i class="fas fa-trash-alt"></i>', ['comments/delete', ['answer', $item->id]], ['class' => 'btn btn-danger', 'html' => true]);

    $table->row([
        ['text' => $item->id],
        ['text' => '<div>' . Url::a(['comments/read', [$item->comment_id]], $message) . '</div><small class="text-muted">&rarr;' . Text::snippet(\App::$Security->strip_tags($item->post->message), 50) . '</small>' , 'html' => true],
        ['text' => Simplify::parseUserLink((int)$item->user_id, $item->guest_name, 'user/update'), 'html' => true],
        ['text' => Date::convertToDatetime($item->created_at, Date::FORMAT_TO_HOUR)],
        ['text' => $btngrp->display(), 'html' => true, 'properties' => ['class' => 'text-center']]
    ]);
}

$table->selectize(0, 'selected');
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= $this->javascript()->submitSelectizeTable('input[name="selected[]"]', 'selected', __('Delete selected'), ['comments/delete', ['answer']], ['class' => 'btn btn-danger']) ?>
<?php if ($moderateIsFound) {
    echo $this->javascript()->submitSelectizeTable('input[name="selected[]"]', 'selected', __('Publish'), ['comments/publish', ['answer']], ['class' => 'btn btn-warning']);
} ?>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<?php $this->stop() ?>
