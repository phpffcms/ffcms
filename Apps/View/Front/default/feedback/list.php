<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var array $pagination */
/** @var \Apps\ActiveRecord\FeedbackPost $records */

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Text;
use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('List requests'),
    'breadcrumbs' => [
        Url::to('/') => __('Home'),
        Url::to('feedback/create') => __('Feedback'),
        __('List requests')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Feedback requests') ?></h1>
<?= $this->insert('feedback/_authTabs'); ?>

<?php
if ($records->count() < 1) {
    echo $this->bootstrap()->alert('warning', __('No requests is founded'));
    $this->stop();
    return;
}

$table = $this->table(['class' => 'table'])
    ->head([
        ['text' => '#'],
        ['text' => __('Message')],
        ['text' => __('Status')],
        ['text' => __('Answers')],
        ['text' => __('Created')],
        ['text' => __('Updated')]
    ]);


foreach ($records as $item) {
    /** @var \Apps\ActiveRecord\FeedbackPost $item */
    $table->row([
        ['text' => $item->id],
        ['text' => Url::a(['feedback/read', [$item->id, $item->hash]], Text::cut($item->message, 0, 40)), 'html' => true],
        ['text' =>
            (int)$item->closed === 1 ?
                '<span class="badge badge-danger">' . __('Closed') . '</span>' :
                '<span class="badge badge-success">' . __('Opened') . '</span>',
            'html' => true, '!secure' => true],
        ['text' => $item->answers()->count()],
        ['text' => Date::convertToDatetime($item->created_at, Date::FORMAT_TO_HOUR)],
        ['text' => Date::convertToDatetime($item->updated_at, Date::FORMAT_TO_HOUR)]
    ]);
}
?>
<div class="table-responsive">
    <?= $table->display(); ?>
</div>

<?= $this->bootstrap()->pagination(['feedback/list'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<?php $this->stop() ?>