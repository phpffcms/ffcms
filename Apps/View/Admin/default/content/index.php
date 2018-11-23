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
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']],
        ['text' => __('Title')],
        ['text' => '<i class="fa fa-comments"></i>', 'html' => true],
        ['text' => __('Date')]
    ]);

$hiddenExist = false;
foreach ($records as $content) {
    // prevent display items with broken category id
    if (!$content->category) {
        continue;
    }
    $frontLink = \App::$Alias->scriptUrl . '/content/read';
    if (!Str::likeEmpty($content->category->path)) {
        $frontLink .= '/' . $content->category->path;
    }
    $frontLink .= '/' . $content->path;

    $controlGroup = '<div class="btn-group btn-group-sm" role="group" aria-label="Control buttons">';
    if (!(bool)$content->display) {
        $controlGroup .= Url::a(['content/display', [$content->id], ['status' => 1]], '<i class="fa fa-eye-slash" style="color: #ff0000;"></i>', [
            'html' => true,
            'class' => 'btn btn-light',
            'data-toggle' => 'tooltip',
            'title' =>  __('Content hidden from regular users')
        ]);
    } else {
        $controlGroup .= Url::a(['content/display', [$content->id], ['status' => 0]], '<i class="fa fa-eye" style="color: #008000;"></i>', [
            'html' => true,
            'class' => 'btn btn-light',
            'data-toggle' => 'tooltip',
            'title' =>  __('Content is public')
        ]);
    }

    if (!(bool)$content->important) {
        $controlGroup .= Url::a(['content/important', [$content->id], ['status' => 1]], '<i class="fa fa-star-o"></i>', [
            'html' => true,
            'class' => 'btn btn-light',
            'data-toggle' => 'tooltip',
            'title' =>  __('Content are not in favorite top. Mark as favorite?')
        ]);
    } else {
        $controlGroup .= Url::a(['content/important', [$content->id], ['status' => 0]], '<i class="fa fa-star" style="color: #c7a922"></i>', [
            'html' => true,
            'class' => 'btn btn-light',
            'data-toggle' => 'tooltip',
            'title' =>  __('Content marked as favorite. Unset this?')
        ]);
    }

    $dropdownControl = '<div class="btn-group btn-group-sm" role="group">';
    $dropdownControl .= '<button id="btn-dropdown-' . $content->id . '" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"> </button>';
    $dropdownControl .= '<div class="dropdown-menu" area-lebeledby="btn-dropdown-' . $content->id . '">';
    $dropdownControl .= Url::a(['content/update', [$content->id]], __('Edit'), ['class' => 'dropdown-item']);
    $dropdownControl .= '<a href="' . $frontLink . '" target="_blank" class="dropdown-item">' . __('See as user') . '</a>';
    if ($type === 'trash') {
        $dropdownControl .= Url::a(['content/restore', [$content->id]], __('Restore'), ['class' => 'dropdown-item']);
    } else {
        $dropdownControl .= Url::a(['content/delete', [$content->id]], __('Delete'), ['class' => 'dropdown-item']);
    }
    $dropdownControl .= '</div></div>';

    $controlGroup .= $dropdownControl;
    $controlGroup .= '</div>';

    // set hidden trigger to true if exist hidden items
    if (!$content->display) {
        $hiddenExist = true;
    }

    $contentInfo = '<div>' . Url::a(['content/update', [$content->id]], $content->getLocaled('title')) . '</div>';
    $contentInfo .= '<div class="small">';
    $contentInfo .= __('Category: <a href="%url%">%name%</a>', ['name' => $content->category->getLocaled('title'), 'url' => Url::to('content/categoryupdate', [$content->category_id])]);
    $contentInfo .= '</div>';

    $table->row([
        ['text' => $content->id, 'html' => true, '!secure' => true],
        ['text' => $controlGroup, 'html' => true, 'properties' => ['class' => 'text-center']],
        ['text' => $contentInfo, 'html' => true],
        ['text' => $content->commentPosts->count()],
        ['text' => Date::convertToDatetime($content->updated_at, Date::FORMAT_TO_SECONDS)]
    ]);
}
$table->selectize(0, 'selected');
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?php if ($type !== 'trash') {
    echo $this->javascript()->submitSelectizeTable('input[name="selected[]"]', 'selected', __('Delete selected'), ['content/globdelete'], ['class' => 'btn btn-danger']);
} ?>
<?php if ($hiddenExist) {
    echo $this->javascript()->submitSelectizeTable('input[name="selected[]"]', 'selected', __('Publish'), ['content/publish'], ['class' => 'btn btn-warning']);
} ?>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>


<?php $this->stop() ?>