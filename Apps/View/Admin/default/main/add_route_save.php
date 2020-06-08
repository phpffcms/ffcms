<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Route saved')
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Congratulations!') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Routing') => ['main/routing'],
    __('Route saved')
]]) ?>

<p><?= __('Route are successful saved! Wait 5 second to update configurations') ?></p>
<?= Url::a(['main/routing'], __('Reload'), ['class' => 'btn btn-primary']); ?>
<script>
    setTimeout(function () {
        window.location.replace("<?= Url::to('main/routing') ?>");
    }, 5000);
</script>
<?php $this->stop() ?>