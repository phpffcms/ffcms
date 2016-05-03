<?php
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;
use Ffcms\Core\Helper\Simplify;

/** @var \Ffcms\Core\Arch\View $this */
/** @var \Apps\ActiveRecord\CommentPost $records */
/** @var \Ffcms\Core\Helper\HTML\SimplePagination $pagination */

$this->title = __('Comments list');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    __('Comments')
];

?>

<?= $this->render('comments/_tabs') ?>

<h1><?= __('Comments list') ?></h1>
<hr />

<?php
if ($records === null || $records->count() < 1) {
    echo '<p class="alert alert-warning">' . __('Comments is not founded') . '</p>';
    return;
}
$items = [];
$moderateIsFound = false;
foreach ($records as $item) {
    $message = Text::cut(\App::$Security->strip_tags($item->message), 0, 75);

    $moderate = (bool)$item->moderate;
    // if even one moderate item is found - change global flag to true
    if ($moderate) {
        $moderateIsFound = true;
    }

    $items[] = [
        1 => ['text' => $item->id],
        2 => ['text' => ($moderate ? '<i class="fa fa-exclamation text-warning"></i> ' : null) . Url::link(['comments/read', $item->id], $message), 'html' => true],
        3 => ['text' => $item->getAnswerCount()],
        4 => ['text' => Simplify::parseUserLink((int)$item->user_id, $item->guest_name, 'user/update'), 'html' => true],
        5 => ['text' => '<a href="' . App::$Alias->scriptUrl . $item->pathway . '" target="_blank">' . Str::sub($item->pathway, 0, 20) . '...</a>', 'html' => true],
        6 => ['text' => Date::convertToDatetime($item->created_at, Date::FORMAT_TO_HOUR)],
        7 => ['text' => Url::link(['comments/read', $item->id], '<i class="fa fa-list fa-lg"></i>') .
            ' ' . Url::link(['comments/delete', 'comment', $item->id], '<i class="fa fa-trash-o fa-lg"></i>'),
            'html' => true, 'property' => ['class' => 'text-center']],
        'property' => [
            'class' => 'checkbox-row' . ($moderate !== false ? ' alert-warning' : null)
        ]
    ];
}

$massAccept = false;
if ($moderateIsFound) {
    $massAccept = [
        'type' => 'submit',
        'class' => 'btn btn-warning',
        'value' => __('Publish'),
        'formaction' => Url::to('comments/publish', 'comment'),
    ];
}

?>

<div class="table-responsive">
<?= Table::display([
    'table' => ['class' => 'table table-bordered table-hover'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Comment')],
            ['text' => __('Answers')],
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
        'form' => ['method' => 'GET', 'class' => 'form-horizontal', 'action' => ''],
        'attachOrder' => 1,
        'selector' => ['type' => 'checkbox', 'name' => 'selected[]', 'class' => 'massSelectId'],
        'buttons' => [
            [
                'type' => 'submit',
                'class' => 'btn btn-danger',
                'value' => __('Delete selected'),
                'formaction' => Url::to('comments/delete', 'comment'),
            ],
            $massAccept
        ],
    ]
]); ?>
</div>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>
