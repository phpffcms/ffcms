<?php

use Ffcms\Templex\Url\Url;


/** @var \Ffcms\Templex\Template\Template $this */
/** @var int $count */

$this->layout('_layouts/default', [
    'title' => __('Clean sessions')
]);

?>

<?php $this->start('body') ?>
<h1><?= __('Clean sessions') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Clean sessions')
]]) ?>

<p><?= __('Are you sure to clear all sessions information? All authorization sessions will be dropped down! Sessions count: %count%', ['count' => $count]) ?></p>
<form action="" method="post">
    <input type="submit" name="clearsessions" value="<?= __('Clear') ?>" class="btn btn-danger" />
</form>
<?php $this->stop() ?>