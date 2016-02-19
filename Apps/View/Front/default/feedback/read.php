<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var \Apps\ActiveRecord\FeedbackPost $post */
/** @var \Apps\ActiveRecord\FeedbackAnswer $answers */
/** @var \Apps\Model\Front\Feedback\FormAnswerAdd|null $model */

$this->title = __('Request from %email%', ['email' => $post->email]);
$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    Url::to('feedback/create') => __('Feedback'),
    __('Read message')
]

?>
    <h1><?= __('Feedback message #%id%', ['id' => $post->id]) ?></h1>
<?php
if (\App::$User->isAuth()) {
    echo $this->render('feedback/_authTabs');
} else {
    echo "<hr />";
}
?>

<?php
if ((int)$post->closed === 0 && \App::$User->isAuth()) {
    $user = App::$User->identity();
    if ($user->getId() === (int)$post->user_id) {
        echo '<div class="pull-right">' .
            Url::link(['feedback/close', $post->id, $post->hash], __('Close request'), ['class' => 'btn btn-danger']) .
            '</div>';
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <strong><?= $post->name ?> (<?= $post->email ?>)</strong>,
                <?= Date::convertToDatetime($post->created_at, Date::FORMAT_TO_HOUR) ?>
            </div>
            <div class="panel-body well">
                <?= Str::replace("\n", "<br />", $post->message) ?>
            </div>
        </div>
    </div>
</div>
<h3><?= __('Answers') ?></h3>
<hr />
<?php if ((int)$post->readed === 0 && ($answers === null || $answers->count() < 1)): ?>
    <p class="alert alert-warning"><?= __('This message is not properly readed by website administrators') ?></p>
<?php endif; ?>

<?php if ($answers !== null && $answers->count() > 0) : ?>
    <?php foreach ($answers as $answer): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel <?= (int)$answer->is_admin === 1 ? 'panel-success' : 'panel-default' ?>">
                    <div class="panel-heading">
                        <strong><?= $answer->name ?> (<?= $answer->email ?>)</strong>,
                        <?= Date::convertToDatetime($answer->created_at, Date::FORMAT_TO_HOUR) ?>
                    </div>
                    <div class="panel-body">
                        <?= Str::replace("\n", "<br />", $answer->message) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<br />
<?php if ((int)$post->closed === 0 && $model !== null): ?>
    <h3><?= __('Add answer') ?></h3>
    <?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']) ?>
    <?= $form->start() ?>

    <?= $form->field('name', 'text', ['class' => 'form-control']) ?>
    <?= $form->field('email', 'text', ['class' => 'form-control']) ?>
    <?= $form->field('message', 'textarea', ['class' => 'form-control', 'rows' => 5]) ?>

    <div class="col-md-offset-3 col-md-9">
        <?= $form->submitButton(__('Add'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?= $form->finish() ?>
<?php else: ?>
<p class="alert alert-danger"><?= __('This request is closed! No answers is allowed') ?></p>
<?php endif; ?>