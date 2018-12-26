<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\Content[] $records */
/** @var string $tag */

$tag = $this->e($tag);

$this->layout('_layouts/default', [
    'title' => $tag,
    'breadcrumbs' => [
        Url::to('/') => __('Home'),
        Url::to('content/list') => __('Contents'),
        __('Tag: %tag%', ['tag' => $tag])
    ]
]);

?>
<?php $this->start('body') ?>

<h1><?= __('Content by tag: %tag%', ['tag' => $tag]) ?></h1>
<hr />
<?php
$listing = $this->listing('ul');

foreach ($records as $item) {
    $listing->li(['text' => $item->getLocaled('title'), 'link' => \App::$Alias->baseUrl . '/content/read/' . $item->getPath()]);
}
echo $listing->display();
?>

<?php $this->stop() ?>
