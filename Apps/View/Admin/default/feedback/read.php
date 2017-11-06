<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var \Apps\ActiveRecord\FeedbackPost $record */
/** @var \Apps\Model\Admin\Feedback\FormAnswerAdd|null $model */

$this->title = __('Feedback read');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('feedback/index') => __('Feedback'),
    __('Read feedback message')
];

echo $this->render('feedback/_tabs');
?>

<h1><?= __('Read feedback message #%id%', ['id' => $record->id]) ?></h1>
<hr />
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-info">
            <div class="panel-heading">
                <?php
                if ((int)$record->readed !== 1) {
                    echo '<i class="fa fa-bell"></i> ';
                }
                ?>
                <?= __('Message sent') ?>: <?= Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR) ?>.
                <?php
                if ((int)$record->closed !== 1) {
                    echo '<span class="label label-success">' . __('Opened') . '</span>';
                } else {
                    echo '<span class="label label-danger">' . __('Closed') . '</span>';
                }
                ?>
            </div>
            <div class="panel-body">
                <?php
                if ((int)$record->closed !== 0) {
                    echo '<p class="alert alert-warning">' . __('The feedback request is closed! Thread in only-read mode') . '.</p>';
                }
                ?>
                <p><?= Str::replace("\n", "<br />", $record->message) ?></p>
                <hr />
                <?php
                // show "mark as read" button if message is not readed
                if ((int)$record->readed !== 1) {
                    echo Url::link(['feedback/turn', 'read', $record->id], __('Mark as read'), ['class' => 'label label-success']) . ' ';
                }

                // show close/open button depend of current status
                if ((int)$record->closed === 0) {
                    echo Url::link(['feedback/turn', 'close', $record->id], __('Close'), ['class' => 'label label-warning']) . ' ';
                } else {
                    echo Url::link(['feedback/turn', 'open', $record->id], __('Open'), ['class' => 'label label-info']) . ' ';
                }

                ?>
                <?= Url::link(['feedback/update', 'post', $record->id], __('Edit'), ['class' => 'label label-primary']) ?>
                <?= Url::link(['feedback/delete', 'post', $record->id], __('Delete'), ['class' => 'label label-danger']) ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-success">
            <div class="panel-heading">
                <?= __('Sender info') ?>
            </div>
            <div class="panel-body">
                <?php
                $uInfo = 'no';
                if ((int)$record->user_id > 0) {
                    $user = \App::$User->identity($record->user_id);
                    if ($user !== null && $user->getId() > 0) {
                        $uInfo = Url::link(['user/update', $user->getId()], $user->profile->getNickname());
                    }
                }
                ?>
                <?= Table::display([
                    'table' => ['class' => 'table table-striped'],
                    'tbody' => [
                        'items' => [
                            [['text' => __('Name')], ['text' => $record->name]],
                            [['text' => __('Email')], ['text' => $record->email]],
                            [['text' => __('Auth')], ['text' => $uInfo, 'html' => true]],
                            [['text' => 'IP'], ['text' => $record->ip]]
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php if ($record->answers->count() > 0): ?>
    <?php foreach ($record->answers->get() as $answer): ?>
        <div class="panel <?= (int)$answer->is_admin === 1 ? 'panel-success' : 'panel-default' ?>">
            <div class="panel-heading">
                <?= __('From') ?>: <?= $answer->name . '(' . $answer->email . ')' . ((int)$answer->user_id > 0 ? Url::link(['user/update', $answer->user_id], '[id' . $answer->user_id . ']') : null) ?>,
                <?= Date::convertToDatetime($answer->created_at, Date::FORMAT_TO_HOUR) ?>
                <span class="pull-right">
                    <?= Url::link(['feedback/update', 'answer', $answer->id], __('Edit'), ['class' => 'label label-primary']) ?>
                    <?= Url::link(['feedback/delete', 'answer', $answer->id], __('Delete'), ['class' => 'label label-danger']) ?>
                </span>
            </div>
            <div class="panel-body">
                <?= Str::replace("\n", "<br />", $answer->message) ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($model !== null): ?>
    <h3><?= __('Add answer') ?></h3>
    <?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']) ?>
    <?= $form->start() ?>

    <?= $form->field('name', 'text', ['class' => 'form-control']) ?>
    <?= $form->field('email', 'email', ['class' => 'form-control']) ?>
    <?= $form->field('message', 'textarea', ['class' => 'form-control', 'rows' => 7]) ?>

    <div class="col-md-offset-3 col-md-9">
        <?= $form->submitButton(__('Add answer'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?= $form->finish() ?>
<?php endif; ?>
