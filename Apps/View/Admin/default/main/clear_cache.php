<?php

use Ffcms\Core\Helper\Url;

/** @var \Ffcms\Core\Arch\View $this */
/** @var float $size */

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Clean cache')
];

$this->title = __('Clean cache');

?>

<h1><?= __('Clean cache') ?></h1>
<hr />
<p><?= __('Are you sure to clear all website cache? Cache size: %size%mb', ['size' => $size]) ?></p>
<form action="" method="post">
    <input type="submit" name="clearcache" value="<?= __('Clear') ?>" class="btn btn-danger" />
</form>