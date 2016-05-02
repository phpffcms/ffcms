<?php

/** @var $records object */
/** @var $pagination object */
/** @var $this object */
/** @var $type string */
use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

$this->title = __('Contents');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    __('Contents')
];

?>

<?= $this->render('content/_tabs') ?>

<h1><?= __('Content list') ?></h1>
<hr />
<div class="row">
    <div class="col-md-12">
        <div class="pull-left">
            <div class="btn-group">
                <button type="button" class="btn btn-default"><?= __('Filters') ?></button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><?= Url::link(['content/index', null, null, ['type' => 'all']], '<i class="fa fa-list"></i> ' . __('All')) ?></li>
                    <li><?= Url::link(['content/index', null, null, ['type' => 'moderate']], '<i class="fa fa-exclamation"></i> ' . __('Moderate')) ?></li>
                    <li><?= Url::link(['content/index', null, null, ['type' => 'trash']], '<i class="fa fa-trash"></i> ' . __('Trash')) ?></li>
                    <li>
                        <a class="trigger right-caret"><i class="fa fa-table"></i> <?= __('Categories') ?></a>
                        <ul class="dropdown-menu sub-menu">
                            <?php foreach (ContentCategory::getSortedCategories() as $id=>$name): ?>
                            <li><?= Url::link(['content/index', null, null, ['type' => $id]], $name) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <?php
        if ($type === 'trash') {
            echo Url::link(['content/clear'], '<i class="fa fa-minus"></i> ' . __('Remove all'), ['class' => 'btn btn-danger pull-right']);
        } else {
            echo Url::link(['content/update', 0], '<i class="fa fa-plus"></i> ' . __('Add content'), ['class' => 'btn btn-primary pull-right']);
        }
        ?>
    </div>
</div>

<?php

if ($records->count() < 1) {
    echo '<p class="alert alert-warning">' . __('Content is not found') . '</p>';
    return;
}

$items = [];
foreach ($records as $content) {
    $frontLink = \App::$Alias->scriptUrl . '/content/read';
    $frontPath = null;
    if (!Str::likeEmpty($content->getCategory()->path)) {
        $frontLink .= '/' . $content->getCategory()->path;
        $frontPath .= '/' . $content->getCategory()->path;
    }
    $frontLink .= '/' . $content->path;
    $frontPath .= '/' . $content->path;
    $frontPath = Str::sub($frontPath, 0, 30);
    $actionIcons = '<a href="' . $frontLink . '" target="_blank"><i class="fa fa-eye fa-lg"></i></a> ';
    $actionIcons .= Url::link(['content/update', $content->id], '<i class="fa fa-pencil fa-lg"></i> ');
    if ($type === 'trash') {
        $actionIcons .= Url::link(['content/restore', $content->id], '<i class="fa fa-refresh fa-lg"></i>');
    } else {
        $actionIcons .= Url::link(['content/delete', $content->id], '<i class="fa fa-trash-o fa-lg"></i>');
    }
    $items[] = [
        'property' => ['class' => 'checkbox-row' . (!(bool)$content->display ? ' alert-warning' : null)],
        1 => ['text' => $content->id, 'html' => true, '!secure' => true],
        2 => ['text' => (!(bool)$content->display ? '<i class="fa fa-exclamation text-warning"></i> ' : null) . Url::link(['content/update', $content->id], Serialize::getDecodeLocale($content->title)), 'html' => true],
        3 => ['text' => Serialize::getDecodeLocale($content->getCategory()->title)],
        4 =>['text' => '<a href="' . $frontLink . '" target="_blank">' . $frontPath . '</a>', 'html' => true],
        5 => ['text' => Date::convertToDatetime($content->updated_at, Date::FORMAT_TO_SECONDS)],
        6 => ['text' => $actionIcons, 'html' => true, 'property' => ['class' => 'text-center']]
    ];
}
?>
<?php
$selectBox = false;
if ($type !== 'trash') {
    $selectBox = [
        'attachOrder' => 1,
        'form' => ['method' => 'GET', 'class' => 'form-horizontal', 'action' => Url::to('content/globdelete')],
        'selector' => ['type' => 'checkbox', 'name' => 'selectRemove[]', 'class' => 'massSelectId'],
        'buttons' => [
            ['type' => 'submit', 'class' => 'btn btn-danger', 'value' => __('Delete selected')]
        ]
    ];
}
?>
<div class="table-responsive">
<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => 'id'],
            ['text' => __('Title')],
            ['text' => __('Category')],
            ['text' => __('Pathway')],
            ['text' => __('Date')],
            ['text' => __('Actions')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ],
    'selectableBox' => $selectBox
])?>
</div>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>