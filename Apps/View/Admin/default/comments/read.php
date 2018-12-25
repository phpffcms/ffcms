<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\CommentPost $record */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('View comment'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('widget/index') => __('Widgets'),
        Url::to('comments/index') => __('Comments'),
        __('View comment')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('comments/_tabs') ?>

<h1><?= __('Read comment #%id%', ['id' => $record->id]) ?></h1>
<div class="card">
    <div class="card-header">
        <?php
        $author = Simplify::parseUserNick($record->user_id, $record->guest_name);
        if ($record->user_id > 0) {
            $author = Url::a(['user/update', [$record->user_id]], $author);
        }
        ?>
        <?= $author . ', ' . Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR) ?>
        <div class="pull-right">
            <?php
            $btngrp = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm']);
            if ($record->moderate) {
                $btngrp->add('<i class="fa fa-eye"></i>', ['comments/publish', ['comment', $record->id]], ['class' => 'btn btn-success', 'html' => true]);
            }
            $btngrp->add('<i class="fa fa-pencil"></i>', ['comments/edit', ['comment', $record->id]], ['class' => 'btn btn-primary', 'html' => true]);
            $btngrp->add('<i class="fa fa-trash-o"></i>', ['comments/delete', ['comment', $record->id]], ['class' => 'btn btn-danger', 'html' => true]);
            echo $btngrp->display();
            ?>
        </div>
    </div>
    <div class="card-body<?= ((bool)$record->moderate ? ' text-warning' : null) ?>">
        <?= $record->message ?>
    </div>
</div>
<?php
/** @var \Apps\ActiveRecord\CommentAnswer[]|\Illuminate\Support\Collection $answers */
$answers = $record->answers;
if (!$answers || $answers->count() < 1) {
    $this->stop();
    return null;
}
?>

<h2><?= __('Comment answers') ?></h2>
<?php foreach ($answers as $answer):?>
    <div class="card" id="answer-<?= $answer->id ?>">
        <div class="card-header">
            <?php
            $answerAuthor = Simplify::parseUserLink($answer->user_id, $answer->guest_name, 'user/update');
            ?>
            <?= $answerAuthor . ', ' . Date::convertToDatetime($answer->created_at, Date::FORMAT_TO_HOUR) ?>
            <div class="pull-right">
                <?php
                $btngrp = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm']);
                if ($answer->moderate) {
                    $btngrp->add('<i class="fa fa-eye"></i>', ['comments/publish', ['answer', $answer->id]], ['class' => 'btn btn-success', 'html' => true]);
                }
                $btngrp->add('<i class="fa fa-pencil"></i>', ['comments/edit', ['answer', $answer->id]], ['class' => 'btn btn-primary', 'html' => true]);
                $btngrp->add('<i class="fa fa-trash-o"></i>', ['comments/delete', ['answer', $answer->id]], ['class' => 'btn btn-danger', 'html' => true]);
                echo $btngrp->display();
                ?>
            </div>
        </div>
        <div class="card-body<?= ((bool)$answer->moderate ? ' text-warning' : null)?>">
            <?= $answer->message ?>
        </div>
    </div>
<?php endforeach; ?>

<?php $this->stop() ?>
