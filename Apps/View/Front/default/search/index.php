<?php

use Ffcms\Core\Helper\Type\Any;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Front\Search\EntitySearchMain $model */
/** @var string $query */

$query = $this->e($query);
$this->layout('_layouts/default', [
    'title' => __('Search: %query%', ['query' => $query]),
    'breadcrumbs' => [
        Url::to('/') => __('Home'),
        __('Search')
    ],
    'query' => $query
]);
?>
<?php $this->start('body') ?>
<h1><?= __('Search query: %query%', ['query' => $query]) ?></h1>
<hr />

<form method="GET" action="<?= Url::to('search/index') ?>" class="form-inline mb-2">
    <div class="form-group col-md-9">
        <label for="search-field" class="sr-only">Query</label>
        <input name="query" type="text" class="form-control col-md" id="search-field" value="<?= $query ?>" maxlength="100">
    </div>
    <button type="submit" class="btn btn-primary col-md"><?= __('Search') ?></button>
</form>

<?php
/** @var \Apps\Model\Front\Search\AbstractSearchResult[] $results */
$results = $model->getRelevanceSortedResult();
if (!Any::isArray($results) || count($results) < 1) {
    echo $this->bootstrap()->alert('warning', __('Matches not founded'));
    $this->stop();
    return;
}
?>

<?php foreach ($results as $item): ?>
    <div class="row mt-1">
        <div class="col-md-12">
            <div class="search-result">
                <div class="h4">
                    <a href="<?= \App::$Alias->baseUrl . $item->getUri() ?>"><?= $model->highlightText($item->getTitle(), 'span', ['class' => 'search-highlight']) ?></a>
                    <small class="float-end text-secondary"><?= $item->getDate() ?></small>
                </div>
                <small><?= $model->highlightText($item->getSnippet(), 'span', ['class' => 'search-highlight']) ?>...</small>
            </div>
        </div>
    </div>
    <hr class="pretty" />
<?php endforeach; ?>

<?php $this->stop() ?>
