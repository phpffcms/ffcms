<?php
/** @var \Ffcms\Templex\Template\Template $this */
/** @var array $links */

$this->layout('_layouts/default', [
    'title' => __('Sitemap')
]);
?>

<?php $this->start('body'); ?>

<h1><?= __('Sitemap') ?></h1>
<hr />

<ul>
<?php foreach ($links as $link): ?>
    <li><a href="<?= $link->uri ?>"><?= $link->title ?? $link->uri ?></a></li>
<?php endforeach; ?>
</ul>
<?php $this->stop(); ?>