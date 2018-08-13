<?php

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;
use Ffcms\Core\Helper\Type\Str;

/** @var \Apps\ActiveRecord\Content[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var string $type */

$this->layout('_layouts/default', [
    'title' => __('Contents'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        __('Contents')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('content/_tabs') ?>

<h1><?= __('Content list') ?></h1>
<div class="row">
    <div class="col-md-6">
        <?php
        if ($type === 'trash') {
            echo Url::a(['content/clear'], '<i class="fa fa-minus"></i> ' . __('Remove all'), ['class' => 'btn btn-danger', 'html' => true]);
        } else {
            echo Url::a(['content/update'], '<i class="fa fa-plus"></i> ' . __('Add content'), ['class' => 'btn btn-primary', 'html' => true]);
        }
        ?>
    </div>
    <div class="col-md-6">
        <div class="pull-right">
            <div class="btn-group" role="group">
                <button id="btnCategories" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-table"></i> <?= __('Categories') ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="btnCategories">
                    <?php
                    foreach (ContentCategory::getSortedCategories() as $id=>$name) {
                        echo Url::a(['content/index', null, ['type' => $id]], $name, ['class' => 'dropdown-item']);
                    }
                    ?>
                </div>
            </div>
            <div class="btn-group" role="group">
                <button id="btnFilters" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-filter"></i> <?= __('Filters') ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="btnCategories">
                    <?= Url::a(['content/index', null, ['type' => 'all']], '<i class="fa fa-list"></i> ' . __('All'), ['class' => 'dropdown-item', 'html' => true]) ?>
                    <?= Url::a(['content/index', null, ['type' => 'moderate']], '<i class="fa fa-exclamation"></i> ' . __('Moderate'), ['class' => 'dropdown-item', 'html' => true]) ?>
                    <?= Url::a(['content/index', null, ['type' => 'trash']], '<i class="fa fa-trash"></i> ' . __('Trash'), ['class' => 'dropdown-item', 'html' => true]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if ($records->count() < 1) {
    echo $this->bootstrap()->alert('warning', __('Content not found'));
    $this->stop();
    return;
}

$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Title')],
        ['text' => __('Category')],
        ['text' => __('Pathway')],
        ['text' => __('Date')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ]);

$hiddenExist = false;
foreach ($records as $content) {
    // prevent display items with broken category id
    if (!$content->category) {
        continue;
    }
    $frontLink = \App::$Alias->scriptUrl . '/content/read';
    $frontPath = null;
    if (!Str::likeEmpty($content->category->path)) {
        $frontLink .= '/' . $content->category->path;
        $frontPath .= '/' . $content->category->path;
    }
    $frontLink .= '/' . $content->path;
    $frontPath .= '/' . $content->path;
    $frontPath = Str::sub($frontPath, 0, 30);
    $actionIcons = '<a href="' . $frontLink . '" target="_blank"><i class="fa fa-eye fa-lg"></i></a> ';
    $actionIcons .= Url::a(['content/update', [$content->id]], '<i class="fa fa-pencil fa-lg"></i> ', ['html' => true]);
    if ($type === 'trash') {
        $actionIcons .= Url::a(['content/restore', [$content->id]], '<i class="fa fa-refresh fa-lg"></i>', ['html' => true]);
    } else {
        $actionIcons .= Url::a(['content/delete', [$content->id]], '<i class="fa fa-trash-o fa-lg"></i>', ['html' => true]);
    }

    // set hidden trigger to true if exist hidden items
    if (!$content->display) {
        $hiddenExist = true;
    }

    $table->row([
        'properties' => ['class' => (!$content->display ? ' alert-warning' : null)],
        ['text' => $content->id, 'html' => true, '!secure' => true],
        ['text' => (!$content->display ? '<i class="fa fa-exclamation text-warning"></i> ' : null) .
            Url::a(['content/update', [$content->id]], $content->getLocaled('title')) .
            ((bool)$content->important ? ' <i class="glyphicon glyphicon-fire"></i>' : null),
            'html' => true],
        ['text' => $content->category->getLocaled('title')],
        ['text' => '<a href="' . $frontLink . '" target="_blank">' . $frontPath . '</a>', 'html' => true],
        ['text' => Date::convertToDatetime($content->updated_at, Date::FORMAT_TO_SECONDS)],
        ['text' => $actionIcons, 'html' => true, 'properties' => ['class' => 'text-center']]
    ]);
}
$table->selectize(0, 'selected');
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= $this->javascript()->submitSelectizeTable('input[name="selected[]"]', 'selected', __('Delete selected'), ['content/globdelete'], ['class' => 'btn btn-danger']) ?>
<?php if ($hiddenExist) {
    echo $this->javascript()->submitSelectizeTable('input[name="selected[]"]', 'selected', __('Delete selected'), ['content/publish'], ['class' => 'btn btn-warning']);
} ?>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>


<?php $this->stop() ?>