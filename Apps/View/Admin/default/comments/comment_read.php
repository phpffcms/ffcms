<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Url;

/** @var \Apps\ActiveRecord\CommentPost $record */
/** @var \Ffcms\Core\Arch\View $this */

$this->title = __('View comment');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    Url::to('comments/index') => __('Comments'),
    __('View comment')
];

?>

<?= $this->render('comments/_tabs') ?>

<h1><?= __('Read comment #%id%', ['id' => $record->id]) ?></h1>
<hr />
<div class="panel panel-info">
    <div class="panel-heading">
        <?php
        $author = Simplify::parseUserNick($record->user_id, $record->guest_name);
        if ($record->user_id > 0) {
            $author = Url::link(['user/update', $record->user_id], $author);
        }
        ?>
        <?= $author . ', ' . Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR)  ?>
        <div class="pull-right">
        	<?php if ((bool)$record->moderate):?>
        		<?= Url::link(['comments/publish', 'comment', $record->id], __('Publish'), ['class' => 'label label-warning']) ?>
        	<?php endif; ?>
            <?= Url::link(['comments/edit', 'comment', $record->id], __('Edit'), ['class' => 'label label-primary']) ?>
            <?= Url::link(['comments/delete', 'comment', $record->id], __('Delete'), ['class' => 'label label-danger']) ?>
        </div>
    </div>
    <div class="panel-body<?= ((bool)$record->moderate ? ' text-warning' : null)?>">
        <?= $record->message ?>
    </div>
</div>
<?php
$answers = $record->getAnswer();
if ($answers === null || $answers->count() < 1) {
    return null;
}
?>

<h2><?= __('Comment answers') ?></h2>
<?php
foreach ($answers->get() as $answer):
/** @var \Apps\ActiveRecord\CommentAnswer $answer */
?>
<div class="panel panel-default" id="answer-<?= $answer->id ?>">
    <div class="panel-heading">
        <?php
        $answerAuthor = Simplify::parseUserLink($answer->user_id, $answer->guest_name, 'user/update');
        ?>
        <?= $answerAuthor . ', ' . Date::convertToDatetime($answer->created_at, Date::FORMAT_TO_HOUR) ?>
        <div class="pull-right">
            <?php if ((bool)$answer->moderate):?>
        		<?= Url::link(['comments/publish', 'answer', $answer->id], __('Publish'), ['class' => 'label label-warning']) ?>
        	<?php endif; ?>
            <?= Url::link(['comments/edit', 'answer', $answer->id], __('Edit'), ['class' => 'label label-primary']) ?>
            <?= Url::link(['comments/delete', 'answer', $answer->id], __('Delete'), ['class' => 'label label-danger']) ?>
        </div>
    </div>
    <div class="panel-body<?= ((bool)$answer->moderate ? ' text-warning' : null)?>">
        <?= $answer->message ?>
    </div>
</div>
<?php endforeach; ?>