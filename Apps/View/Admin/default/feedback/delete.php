<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Url;

/** @var \Apps\ActiveRecord\FeedbackPost|\Apps\ActiveRecord\FeedbackAnswer $record */
/** @var \Apps\Model\Admin\Feedback\FormAnswerAdd|null $model */

$this->title = __('Feedback delete');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('feedback/index') => __('Feedback'),
    __('Delete message')
];

echo $this->render('feedback/_tabs');
?>
<h1><?= __('Delete feedback message') ?></h1>
<hr />
<div class="table-responsive">
    <?= Table::display([
        'table' => ['class' => 'table table-bordered'],
        'tbody' => [
            'items' => [
                [['text' => __('Sender')], ['text' => $record->name . ' (' . $record->email . ')']],
                [['text' => __('Date')], ['text' => Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR)]],
                [['text' => __('Message')], ['text' => $record->message]]
            ]
        ]
    ]) ?>
</div>
<form action="" method="post">
    <input type="submit" name="deleteFeedback" value="<?= __('Delete') ?>" class="btn btn-danger" />
</form>
