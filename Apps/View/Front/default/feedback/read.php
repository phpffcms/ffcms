<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\FeedbackPost $post */
/** @var \Apps\ActiveRecord\FeedbackAnswer $answers */
/** @var \Apps\Model\Front\Feedback\FormAnswerAdd|null $model */

$this->layout('_layouts/default', [
    'title' => __('Request from %email%', ['email' => $post->email]),
    'breadcrumbs' => [
        Url::to('/') => __('Home'),
        Url::to('feedback/create') => __('Feedback'),
        __('Read message')
    ]
]);
?>
<?php $this->start('body') ?>
<h1><?= __('Feedback message #%id%', ['id' => $post->id]) ?></h1>
<?php if (\App::$User->isAuth()): ?>
    <?= $this->insert('feedback/_authTabs') ?>
<?php else: ?>
    <hr />
<?php endif; ?>

<?php
if (!(bool)$post->closed && \App::$User->isAuth()) {
    $user = App::$User->identity();
    if ((int)$user->getId() === (int)$post->user_id) {
       echo Url::a(['feedback/close', [$post->id, $post->hash]], __('Close request'), ['class' => 'btn btn-danger']);
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong><?= $post->name ?> (<?= $post->email ?>)</strong>,
                <?= Date::convertToDatetime($post->created_at, Date::FORMAT_TO_HOUR) ?>
            </div>
            <div class="card-body well">
                <?= Str::replace("\n", "<br />", $post->message) ?>
            </div>
        </div>
    </div>
</div>
<h3><?= __('Answers') ?></h3>
<hr />
<?php if (!(bool)$post->readed && (!$answers || $answers->count() < 1)): ?>
    <?= $this->bootstrap()->alert('warning', __('This message is not properly readed by website administrators')) ?>
<?php endif; ?>

<?php if ($answers && $answers->count() > 0) : ?>
    <?php foreach ($answers as $answer): ?>
        <div class="row" id="feedback-answer-<?= $answer->id ?>">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header<?= (bool)$answer->is_admin ? ' bg-success' : null ?>">
                        <strong><?= $answer->name ?> (<?= $answer->email ?>)</strong>,
                        <?= Date::convertToDatetime($answer->created_at, Date::FORMAT_TO_HOUR) ?>
                    </div>
                    <div class="card-body">
                        <?= Str::replace("\n", "<br />", $answer->message) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<br />
<?php if (!(bool)$post->closed && $model): ?>
    <h3><?= __('Add answer') ?></h3>
    <?php $form = $this->form($model) ?>
    <?= $form->start() ?>

    <?= $form->fieldset()->text('name') ?>
    <?= $form->fieldset()->text('email') ?>
    <?= $form->fieldset()->textarea('message', ['rows' => 5]) ?>

    <?= $form->button()->submit(__('Add'), ['class' => 'btn btn-primary']) ?>

    <?= $form->stop() ?>
<?php else: ?>
    <?= $this->bootstrap()->alert('danger', __('This request is closed! No answers is allowed')) ?>
<?php endif; ?>

<?php $this->stop() ?>
