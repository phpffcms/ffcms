<?php

use Ffcms\Core\Helper\Url;

$this->title = __('Close request');
$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    Url::to('feedback/create') => __('Feedback'),
    __('Close request')
]
?>
<h1><?= __('Close feedback request #%id%', ['id' => \App::$Request->getID()]) ?></h1>
<hr />
<p><?= __('Are you sure to close this feedback request?') ?></p>
<form action="" method="post">
    <input type="submit" name="closeRequest" value="<?= __('Close') ?>" class="btn btn-danger" />
    <?= Url::link(['feedback/read', \App::$Request->getID(), \App::$Request->getAdd()], __('Cancel'), ['class' => 'btn btn-info']) ?>
</form>