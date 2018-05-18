<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var int $id */
/** @var string $hash */

$this->layout('_layouts/default', [
    'title' => __('Close request'),
    'breadcrumbs' => [
        Url::to('/') => __('Home'),
        Url::to('feedback/create') => __('Feedback'),
        __('Close request')
    ]
]);
?>
<?php $this->start('body') ?>
<h1><?= __('Close feedback request #%id%', ['id' => $id]) ?></h1>
<hr />
<p><?= __('Are you sure to close this feedback request?') ?></p>
<form action="" method="post">
    <input type="submit" name="closeRequest" value="<?= __('Close') ?>" class="btn btn-danger" />
    <?= Url::a(['feedback/read', [$id, $hash]], __('Cancel'), ['class' => 'btn btn-info']) ?>
</form>
<?php $this->stop() ?>