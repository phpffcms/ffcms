<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Url;

/** @var $this \Ffcms\Core\Arch\View */
/** @var $records object */
/** @var $pagination object */

$this->title = __('My content');

$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    Url::to('content/index') => __('Contents'),
    __('My content')
];

?>

<h1><?= __('My content')?></h1>
<hr />
<?php
if ($records->count() < 1) {
    echo __('Content is not found yet');
    return;
}
$items = [];
foreach ($records as $record) {
    $moderate = (int)$record->display === 0;
    $title = Serialize::getDecodeLocale($record->title);
    if ($moderate) {
        $title = Url::link(['content/update', $record->id], $title) . ' <i class="fa fa-pencil"></i>';
    }

    $items[] = [
        ['text' => $record->id],
        ['type' => 'text', 'text' => $title, 'html' => true],
        ['text' => $moderate ? __('No') : __('Yes')],
        ['text' => Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR)],
        'property' => ['class' => $moderate ? 'text-warning' : 'text-success']
    ];
}
?>

<p><?= __('Remember you can edit content only on moderate stage!') ?></p>

<div class="table-responsive"><?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Title')],
            ['text' => __('Accepted')],
            ['text' => __('Date')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ]
]); ?></div>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>

<?= Url::link(['content/update'], __('Add content'), ['class' => 'btn btn-primary']) ?>
