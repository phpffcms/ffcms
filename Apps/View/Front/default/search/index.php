<?php

use Ffcms\Core\Helper\Url;

/** @var \Ffcms\Core\Arch\View $this */
/** @var \Apps\Model\Front\Search\EntitySearchMain $model */
/** @var string $query */

$this->title = __('Search: %query%', ['query' => $query]);
$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    __('Search')
];
?>

<h1><?= __('Search query: %query%', ['query' => $query]) ?></h1>
<hr />
<form class="form-horizontal" method="get" action="">
    <div class="form-group">
        <div class="col-md-10">
            <input name="query" class="form-control" type="text" value="<?= $query ?>" maxlength="100">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-default btn-block"><?= __('Search') ?></button>
        </div>
    </div>
</form>

<?php
$results = $model->getRelevanceSortedResult();

if (!\Ffcms\Core\Helper\Type\Obj::isArray($results) || count($results) < 1) {
    echo '<p class="alert alert-warning">' . __('Matches not founded') . '</p>';
    return;
}
?>

<?php foreach ($results as $item): ?>
<div class="row">
    <div class="col-md-12">
        <div class="search-result">
            <div class="h4">
                <a href="<?= \App::$Alias->baseUrl . $item['uri'] ?>"><?= $item['title'] ?></a>
                <small class="pull-right"><?= $item['date'] ?></small>
            </div>
            <small><?= $item['snippet'] ?>...</small>
        </div>
    </div>
</div>
<hr class="pretty" />
<?php endforeach; ?>