<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
use Ffcms\Core\Helper\Type\Str;

if (Str::likeEmpty($rating)) {
    $rating = 0;
}

$items = [];
$items[] = ['type' => 'link', 'link' => ['profile/index', 'all'], 'text' => __('All')];
if ($rating === 1) {
    $items[] = ['type' => 'link', 'link' => ['profile/index', 'rating'], 'text' => __('Rating')];
}
$items[] = ['type' => 'link', 'link' => ['profile/search'], 'text' => __('Search')];
$items[] = ['type' => 'link', 'link' => ['profile/feed'], 'text' => __('Feed'), 'property' => ['class' => 'pull-right']];
?>

<?= Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'activeOrder' => 'id',
    'items' => $items
]);
?>