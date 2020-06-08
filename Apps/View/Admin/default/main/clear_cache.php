<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var float $size */

$this->layout('_layouts/default', [
    'title' => __('Clean cache')
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Clean cache') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Clean cache')
]]) ?>

<p><?= __('Are you sure to clear all website cache?') ?></p>
<form action="" method="post">
    <input type="submit" name="clearcache" value="<?= __('Clear') ?>" class="btn btn-danger" />
</form>
<?php $this->stop() ?>