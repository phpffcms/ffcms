<?php

use Ffcms\Core\Helper\Url;

$this->title = __('Comments list');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    __('Comments')
];

?>

<?= $this->render('comments/_tabs') ?>

<h1>Comments list</h1>
<hr />