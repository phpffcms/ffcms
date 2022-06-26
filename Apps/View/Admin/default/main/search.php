<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Main\CollectionSearchResults $model */

$query = $this->e($model->getQuery());

$this->layout('_layouts/default', [
    'title' => __('Search: %query%', ['query' => $query]),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        __('Search')
    ],
    'query' => $query
]);

?>

<?php $this->start('body') ?>

<h1><?= __('Search: %query%', ['query' => $query]) ?></h1>
<div class="row">
    <div class="col">
        <form class="form-inline" method="get" action="<?= Url::link(['main/search']) ?>">
            <input type="text" class="form-control col" name="search" placeholder="<?= __('Enter search query') ?>" value="<?= $query ?>">&nbsp;
            <input type="submit" name="submit" value="<?= __('Search') ?>" class="btn btn-secondary" />
        </form>
    </div>
</div>

<?php
$result = $model->getRelevanceBasedResult();
if (!$result || count($result) < 1) {
    echo $this->bootstrap()->alert('warning', __('Nothing found'));
    $this->stop();
    return;
}
?>
<?php foreach ($result as $item): ?>
<?php /** @var \Apps\Model\Admin\Main\AbstractSearchItem $item */ ?>
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="search-result">
                <div class="h4">
                    <a href="<?= $item->getUrl() ?>">
                        <?= $model->highlightText($item->getTitle(), 'span', ['class' => 'search-highlight']) ?>
                    </a>
                    <span class="badge badge-secondary"><?= $item->getMarker() ?></span>
                    <small class="float-end text-secondary"><?= $item->getDate() ?></small>
                </div>
                <p class="text-muted"><?= $model->highlightText($item->getSnippet(), 'span', ['class' => 'search-highlight']) ?>...</p>
            </div>
        </div>
    </div>
    <hr class="mt-0 pt-0" />
<?php endforeach; ?>


<?php $this->stop() ?>
