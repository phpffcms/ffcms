<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Url;

/** @var \Apps\ActiveRecord\FeedbackPost $records */
/** @var \Ffcms\Core\Helper\HTML\SimplePagination $pagination */

$this->title = __('Feedback list');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    __('Feedback')
];

echo $this->render('feedback/_tabs');

?>

<h1><?= __('Feedback list') ?></h1>
<hr />
<?php if ($records === null || $records->count() < 1) {
    echo '<p class="alert alert-warning">' . __('Feedback requests is empty now!') . '</p>';
    return;
}

$items = [];
foreach ($records as $item) {
    /** @var \Apps\ActiveRecord\FeedbackPost $item*/
    $items[] = [
        ['text' => $item->id . ((int)$item->readed !== 1 ? ' <i class="fa fa-bell alert-info"></i>'  : null), 'html' => true],
        ['text' => Url::link(['feedback/read', $item->id], Text::snippet($item->message, 40, false)), 'html' => true],
        ['text' => $item->getAnswers()->count()],
        ['text' => $item->email],
        ['text' =>
            (int)$item->closed === 1 ?
                '<span class="label label-danger">' . __('Closed') . '</span>' :
                '<span class="label label-success">' . __('Opened') . '</span>',
            'html' => true, '!secure' => true],
        ['text' => Date::convertToDatetime($item->updated_at, Date::FORMAT_TO_HOUR)],
        ['text' => Url::link(['feedback/read', $item->id], '<i class="fa fa-list fa-lg"></i> ') .
            Url::link(['feedback/delete', 'post', $item->id], '<i class="fa fa-trash-o fa-lg"></i>'),
            'html' => true, 'property' => ['class' => 'text-center']]
    ];
}
?>

<div class="table table-responsive">
<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Text')],
            ['text' => __('Answers')],
            ['text' => __('Author')],
            ['text' => __('Status')],
            ['text' => __('Date')],
            ['text' => __('Actions')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ]
]) ?>
</div>

<p><i class="fa fa-bell alert-info"></i> = <?= __('New request or new answer in feedback topic') ?></p>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>

