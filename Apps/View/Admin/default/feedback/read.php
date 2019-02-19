<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\FeedbackPost $record */
/** @var \Apps\Model\Admin\Feedback\FormAnswerAdd|null $model */
/** @var \Ffcms\Templex\Template\Template $this */


$this->layout('_layouts/default', [
    'title' => __('Feedback read'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('feedback/index') => __('Feedback'),
        __('Read feedback message')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('feedback/_tabs') ?>

<h1><?= __('Read feedback message #%id%', ['id' => $record->id]) ?></h1>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header" style="background-color: #cadcee">
                <?php
                if (!(bool)$record->readed) {
                    echo '<i class="fas fa-bell"></i> ';
                }
                ?>
                <?= __('Message sent') ?>: <?= Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR) ?>.
                <?php
                if (!(bool)$record->closed) {
                    echo '<span class="badge badge-success">' . __('Opened') . '</span>';
                } else {
                    echo '<span class="badge badge-danger">' . __('Closed') . '</span>';
                }
                ?>
                <?php
                $btngrp = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm', 'role' => 'group'], 4);

                // show "mark as read" button if message is not readed
                if (!(bool)$record->readed) {
                    $btngrp->add('<i class="far fa-bookmark"></i>', ['feedback/turn', ['read', $record->id]], ['class' => 'btn btn-success', 'html' => true, 'data-toggle' => 'tooltip', 'title' => __('Mark as read')]);
                }

                // show close/open button depend of current status
                if (!(bool)$record->closed) {
                    $btngrp->add('<i class="fas fa-eye-slash"></i>', ['feedback/turn', ['close', $record->id]], ['class' => 'btn btn-warning', 'html' => true, 'data-toggle' => 'tooltip', 'title' => __('Close')]);
                } else {
                    $btngrp->add('<i class="fas fa-eye"></i>', ['feedback/turn', ['open', $record->id]], ['class' => 'btn btn-info', 'html' => true, 'data-toggle' => 'tooltip', 'title' => __('Open')]);
                }

                $btngrp->add('<i class="fas fa-pencil-alt"></i>', ['feedback/update', ['post', $record->id]], ['class' => 'btn btn-primary', 'html' => true, 'data-toggle' => 'tooltip', 'title' => __('Edit')]);
                $btngrp->add('<i class="fas fa-trash-alt"></i>', ['feedback/delete', ['post', $record->id]], ['class' => 'btn btn-danger', 'html' => true, 'data-toggle' => 'tooltip', 'title' => __('Delete')]);
                ?>
                <div class="pull-right"><?= $btngrp->display() ?></div>
            </div>
            <div class="card-body">
                <?php
                if ((bool)$record->closed) {
                    echo '<p class="alert alert-warning">' . __('The feedback request is closed! Thread in only-read mode') . '.</p>';
                }
                ?>
                <p><?= Str::replace("\n", "<br />", $record->message) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <?= __('Sender info') ?>
            </div>
            <div class="card-body">
                <?php
                $uInfo = 'no';
                if ((int)$record->user_id > 0) {
                    $user = \App::$User->identity($record->user_id);
                    if ($user && $user->getId() > 0) {
                        $uInfo = Url::a(['user/update', [$user->getId()]], $user->profile->getNickname());
                    }
                }
                ?>
                <?= $this->table(['class' => 'table table-striped'])
                    ->body([
                        [['text' => __('Name')], ['text' => $record->name]],
                        [['text' => __('Email')], ['text' => $record->email]],
                        [['text' => __('Auth')], ['text' => $uInfo, 'html' => true]],
                        [['text' => 'IP'], ['text' => $record->ip]]
                    ])->display(); ?>
            </div>
        </div>
    </div>
</div>

<?php if ($record->answers->count() > 0): ?>
    <?php foreach ($record->answers as $answer): ?>
        <div class="card">
            <div class="card-header">
                <?= __('From') ?>: <?= $answer->name . '(' . $answer->email . ')' . ((int)$answer->user_id > 0 ? Url::a(['user/update', [$answer->user_id]], '[id' . $answer->user_id . ']') : null) ?>,
                <?= Date::convertToDatetime($answer->created_at, Date::FORMAT_TO_HOUR) ?>
                <div class="pull-right">
                    <?= $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm'])
                        ->add('<i class="fas fa-pencil-alt"></i>', ['feedback/update', ['answer', $answer->id]], ['class' => 'btn btn-primary', 'html' => true])
                        ->add('<i class="fas fa-trash-alt"></i>', ['feedback/delete', ['answer', $answer->id]], ['class' => 'btn btn-danger', 'html' => true])
                        ->display() ?>
                </div>
            </div>
            <div class="card-body">
                <?= Str::replace("\n", "<br />", $answer->message) ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($model): ?>
    <h3><?= __('Add answer') ?></h3>
    <?php $form = $this->form($model) ?>
    <?= $form->start() ?>

    <?= $form->fieldset()->text('name') ?>
    <?= $form->fieldset()->text('email') ?>
    <?= $form->fieldset()->textarea('message', ['class' => 'form-control', 'rows' => 7]) ?>

    <?= $form->button()->submit(__('Add answer'), ['class' => 'btn btn-primary']) ?>

    <?= $form->stop() ?>
<?php endif; ?>

<?php $this->stop() ?>
