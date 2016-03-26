<?php
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var \Apps\ActiveRecord\CommentAnswer $records */
/** @var \Ffcms\Core\Helper\HTML\SimplePagination $pagination */

$this->title = __('Answers list');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    Url::to('comments/index') => __('Comments'),
    __('Answers')
];

?>

<?= $this->render('comments/_tabs') ?>

<h1><?= __('Answers list') ?></h1>
<hr />

<?php
if ($records === null || $records->count() < 1) {
    echo '<p class="alert alert-warning">' . __('Answers is not founded') . '</p>';
    return;
}
$items = [];
foreach ($records as $item) {
    $commentObject = $item->getCommentPost();
    $message = Text::cut(\App::$Security->strip_tags($item->message), 0, 75);
    $userArr = [];
    if ((int)$item->user_id > 0 && \App::$User->isExist($item->user_id)) {
        $userName = \App::$User->identity($item->user_id)->getProfile()->getNickname();
        $userArr = ['text' => Url::link(['user/update', $item->user_id], $userName), 'html' => true];
    }

    $items[] = [
        1 => ['text' => $item->id],
        2 => ['text' => Url::link(['comments/read', $commentObject->id, null, ['#' => '#answer-' . $item->id]], $message), 'html' => true],
        3 => $userArr,
        4 => ['text' => '<a href="' . App::$Alias->scriptUrl . $commentObject->pathway . '" target="_blank">' . Str::sub($commentObject->pathway, 0, 20) . '...</a>', 'html' => true],
        5 => ['text' => Date::convertToDatetime($item->created_at, Date::FORMAT_TO_HOUR)],
        6 => ['text' => Url::link(['comments/read', $commentObject->id], '<i class="fa fa-list fa-lg"></i>') .
            ' ' . Url::link(['comments/delete', 'answer', $item->id], '<i class="fa fa-trash-o fa-lg"></i>'),
            'html' => true, 'property' => ['class' => 'text-center']],
        'property' => ['class' => 'checkbox-row']
    ];
}

?>

<div class="table-responsive">
<?= Table::display([
    'table' => ['class' => 'table table-bordered table-hover'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Answer')],
            ['text' => __('Author')],
            ['text' => __('Page')],
            ['text' => __('Date')],
            ['text' => __('Actions')],
        ]
    ],
    'tbody' => [
        'items' => $items
    ],
    'selectableBox' => [
        'attachOrder' => 1,
        'form' => ['method' => 'GET', 'class' => 'form-horizontal', 'action' => Url::to('comments/delete', 'answer')],
        'input' => ['type' => 'checkbox', 'name' => 'selectRemove[]', 'class' => 'massSelectId'],
        'button' => ['type' => 'submit', 'class' => 'btn btn-danger', 'value' => __('Delete selected')]
    ]
]); ?>
</div>