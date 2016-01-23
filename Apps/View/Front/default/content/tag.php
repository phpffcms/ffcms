<?php

use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var \Apps\ActiveRecord\Content $records */
/** @var string $tag */

$this->title = $tag;
$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    Url::to('content/list') => __('Contents'),
    __('Tag: %tag%', ['tag' => $tag])
];

?>
<h1><?= __('Content by tag: %tag%', ['tag' => $tag]) ?></h1>
<hr />
<?php
$items = [];
foreach ($records as $item) {
    /** @var \Apps\ActiveRecord\Content $item*/
    $items[] = [
        'text' => Serialize::getDecodeLocale($item->title),
        'link' => \App::$Alias->baseUrl . '/content/read/' . $item->getPath()
    ];
}
?>

<?= \Ffcms\Core\Helper\HTML\Listing::display([
    'type' => 'ul',
    'items' => $items
]); ?>