<?php

use Ffcms\Core\Helper\HTML\Listing;
use Ffcms\Core\Helper\Url;

/** @var \Apps\Model\Front\Profile\EntityNotificationsList $model */

$this->title = __('My notifications');
$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    Url::to('profile/show', \App::$User->identity()->id) => __('Profile'),
    $this->title
];

?>
<h1><?= __('Notifications') ?></h1>
<hr />
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <?= Listing::display([
                'type' => 'ul',
                'property' => ['class' => 'list-inline'],
                'items' => [
                    ['type' => 'link', 'link' => ['profile/notifications', 'all'], 'text' => __('All')],
                    ['type' => 'link', 'link' => ['profile/notifications', 'unread'], 'text' => __('Unread')],
                ]
            ]) ?>
        </div>
    </div>
</div>
<?php
if ($model->items === null || count($model->items) < 1) {
    echo '<p class="alert alert-warning">' . __('No notifications available') . '</p>';
    return;
}
?>
<?php foreach ($model->items as $item): ?>
    <div class="notice<?= $item['new'] ? ' notice-new' : ''; ?>">
        <span class="label label-info"><?= $item['date'] ?></span> <?= $item['text'] ?>
    </div>
<?php endforeach; ?>
