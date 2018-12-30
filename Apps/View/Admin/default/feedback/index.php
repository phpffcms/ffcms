<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Text;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\FeedbackPost $records */
/** @var array $pagination */

$this->layout('_layouts/default', [
    'title' => __('Feedback list'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        __('Feedback')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('feedback/_tabs') ?>

<h1><?= __('Feedback list') ?></h1>
<?php
if (!$records || $records->count() < 1) {
    echo '<p class="alert alert-warning">' . __('Feedback requests is empty now!') . '</p>';
    $this->stop();
    return;
}

$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Text')],
        ['text' => __('Answers')],
        ['text' => __('Author')],
        ['text' => __('Status')],
        ['text' => __('Date')],
        ['text' => __('Actions')]
    ]);

$items = [];
foreach ($records as $item) {
    /** @var \Apps\ActiveRecord\FeedbackPost $item*/
    $table->row([
        ['text' => $item->id . (!$item->readed ? ' <i class="fa fa-bell alert-info"></i>'  : null) . ($item->closed ? ' <i class="fa fa-eye-slash alert-danger"></i>' : null), 'html' => true],
        ['text' => Url::a(['feedback/read', [$item->id]], Text::snippet($item->message, 40)), 'html' => true],
        ['text' => '<span class="badge badge-light">' . $item->answers->count() . '</span>', 'html' => true],
        ['text' => $item->email],
        ['text' => (bool)$item->closed ? '<span class="badge badge-danger">' . __('Closed') . '</span>' : '<span class="label label-success">' . __('Opened') . '</span>', 'html' => true, '!secure' => true],
        ['text' => Date::convertToDatetime($item->updated_at, Date::FORMAT_TO_HOUR)],
        ['text' => $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm', 'role' => 'group'])
            ->add('<i class="fa fa-feed"></i>', ['feedback/read', [$item->id]], ['class' => 'btn btn-light', 'html' => true])
            ->add('<i class="fa fa-trash-o"></i>', ['feedback/delete', ['post', $item->id]], ['class' => 'btn btn-danger', 'html' => true])
            ->display(), 'html' => true, 'property' => ['class' => 'text-center']]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<p><i class="fa fa-bell alert-info"></i> = <?= __('New request or new answer in feedback topic') ?></p>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<?php $this->stop() ?>
