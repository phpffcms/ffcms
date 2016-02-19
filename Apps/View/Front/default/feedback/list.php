<?php

/** @var \Ffcms\Core\Helper\HTML\SimplePagination $pagination */
/** @var \Apps\ActiveRecord\FeedbackPost $records */

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Url;

$this->title = __('List requests');
$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    Url::to('feedback/create') => __('Feedback'),
    __('List requests')
]

?>

<h1><?= __('Feedback requests') ?></h1>
<?= $this->render('feedback/_authTabs'); ?>

<?php
if ($records->count() < 1) {
    echo '<p class="alert alert-warning">' . __('No requests is founded') . '</p>';
    return;
}
$items = [];
foreach ($records as $item) {
    $items[] = [
        ['text' => $item->id],
        ['text' => Url::link(['feedback/read', $item->id, $item->hash], Text::cut($item->message, 0, 40)), 'html' => true],
        ['text' =>
            (int)$item->closed === 1 ?
                '<span class="label label-danger">' . __('Closed') . '</span>' :
                '<span class="label label-success">' . __('Opened') . '</span>',
            'html' => true, '!secure' => true],
        ['text' => Date::convertToDatetime($item->created_at, Date::FORMAT_TO_HOUR)],
        ['text' => Date::convertToDatetime($item->updated_at, Date::FORMAT_TO_HOUR)]
    ];
}

?>

<?= \Ffcms\Core\Helper\HTML\Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Message')],
            ['text' => __('Status')],
            ['text' => __('Created')],
            ['text' => __('Updated')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ]
]) ?>


<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>
