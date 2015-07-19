<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
use Ffcms\Core\Helper\Type\String;

if (String::likeEmpty($rating)) {
    $rating = 0;
}

$items = [];
$items[] = ['type' => 'link', 'link' => ['profile/index', 'all'], 'text' => __('All')];
if ($rating === 1) {
    $items[] = ['type' => 'link', 'link' => ['profile/index', 'rating'], 'text' => __('Rating')];
}
$items[] = ['type' => 'link', 'link' => ['profile/search'], 'text' => __('Search')];
?>

<?= Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'activeOrder' => 'id',
    'items' => $items
]);
?>