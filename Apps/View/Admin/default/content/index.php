<?php

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\Content[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var string $type */

$this->layout('_layouts/default', [
    'title' => __('Contents')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Content list') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Contents')
]]) ?>

<?= $this->insert('content/_tabs') ?>

<div class="row my-2">
    <div class="col-md-6">
        <?php
        if ($type === 'trash') {
            echo Url::a(['content/clear'], '<i class="fas fa-minus"></i> ' . __('Remove all'), ['class' => 'btn btn-danger', 'html' => true]);
        } else {
            echo Url::a(['content/update'], '<i class="fas fa-plus"></i> ' . __('Add content'), ['class' => 'btn btn-primary', 'html' => true]);
        }
        ?>
    </div>
    <div class="col-md-6">
        <div class="float-end">
            <div class="btn-group" role="group">
                <button id="btnCategories" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-table"></i> <?= __('Categories') ?>
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
                    <i class="fas fa-filter"></i> <?= __('Filters') ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="btnCategories">
                    <?= Url::a(['content/index', null, ['type' => 'all']], '<i class="fas fa-list"></i> ' . __('All'), ['class' => 'dropdown-item', 'html' => true]) ?>
                    <?= Url::a(['content/index', null, ['type' => 'moderate']], '<i class="fas fa-exclamation"></i> ' . __('Moderate'), ['class' => 'dropdown-item', 'html' => true]) ?>
                    <?= Url::a(['content/index', null, ['type' => 'trash']], '<i class="fas fa-trash-alt"></i> ' . __('Trash'), ['class' => 'dropdown-item', 'html' => true]) ?>
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
        ['text' => '<i class="fas fa-comments"></i>', 'html' => true],
        ['text' => __('Date')]
    ], ['class' => 'thead-dark']);

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

    $actionMenu = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm', 'dropdown' => ['class' => 'btn-group btn-group-sm']], 2);
    if (!(bool)$content->display) {
        $actionMenu->add('<i class="fas fa-eye-slash" style="color: #ff0000;"></i>', ['content/display', [$content->id], ['status' => 1]], [
            'html' => true,
            'class' => 'btn btn-light',
            'data-toggle' => 'tooltip',
            'title' =>  __('Content hidden from regular users')
        ]);
    } else {
        $actionMenu->add('<i class="fas fa-eye" style="color: #008000;"></i>', ['content/display', [$content->id], ['status' => 0]], [
            'html' => true,
            'class' => 'btn btn-light',
            'data-toggle' => 'tooltip',
            'title' =>  __('Content is public')
        ]);
    }

    if (!(bool)$content->important) {
        $actionMenu->add('<i class="far fa-star"></i>', ['content/important', [$content->id], ['status' => 1]], [
            'html' => true,
            'class' => 'btn btn-light',
            'data-toggle' => 'tooltip',
            'title' =>  __('Content are not in favorite top. Mark as favorite?')
        ]);
    } else {
        $actionMenu->add('<i class="fas fa-star" style="color: #c7a922"></i>', ['content/important', [$content->id], ['status' => 0]], [
            'html' => true,
            'class' => 'btn btn-light',
            'data-toggle' => 'tooltip',
            'title' =>  __('Content marked as favorite. Unset this?')
        ]);
    }

    $actionMenu->add(__('Edit'), ['content/update', [$content->id]]);
    $actionMenu->add(__('See as user'), [$frontLink], ['target' => '_blank']);

    $actionMenu->add(__('Clone'), ['content/update', null, ['from' => $content->id]]);

    if ($type === 'trash') {
        $actionMenu->add(__('Restore'), ['content/restore', [$content->id]]);
    } else {
        $actionMenu->add(__('Delete'), ['content/delete', [$content->id]]);
    }

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
        ['text' => $actionMenu->display(), 'html' => true, 'properties' => ['class' => 'text-center']],
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