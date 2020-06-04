<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\FeedbackPost|\Apps\ActiveRecord\FeedbackAnswer $record */
/** @var \Apps\Model\Admin\Feedback\FormAnswerAdd|null $model */

$this->layout('_layouts/default', [
    'title' => __('Feedback delete'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('feedback/index') => __('Feedback'),
        __('Delete message')
    ]
]);
?>
<?php $this->start('body') ?>

<?= $this->insert('feedback/_tabs') ?>

<h1><?= __('Delete feedback message') ?></h1>

<div class="table-responsive">
    <?= $this->table(['class' => 'table table-bordered'])
        ->body([
            [['text' => __('Sender')], ['text' => $record->name . ' (' . $record->email . ')']],
            [['text' => __('Date')], ['text' => Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR)]],
            [['text' => __('Message')], ['text' => $record->message]]
        ])->display() ?>
</div>
<form action="" method="post">
    <input type="submit" name="deleteFeedback" value="<?= __('Delete') ?>" class="btn btn-danger" />
    <?= Url::a(['feedback/index'], __('Cancel'), ['class' => 'btn btn-light']); ?>
</form>

<?php $this->stop() ?>