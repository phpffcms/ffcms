<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var \Ffcms\Core\Arch\View $this */
/** @var int $count */

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Clean sessions')
];

$this->title = __('Clean sessions');

?>

<h1><?= __('Clean sessions') ?></h1>
<hr />
<p><?= __('Are you sure to clear all sessions information? All authorization sessions will be dropped down! Sessions count: %count%', ['count' => $count]) ?></p>
<form action="" method="post">
    <input type="submit" name="clearsessions" value="<?= __('Clear') ?>" class="btn btn-danger" />
</form>