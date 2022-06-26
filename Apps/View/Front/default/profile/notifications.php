<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Front\Profile\EntityNotificationsList $model */
/** @var array $pagination */
/** @var string $type */

$this->layout('_layouts/default', [
    'title' => __('My notifications'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        Url::to('profile/show', [\App::$User->identity()->getId()]) => __('Profile'),
        __('My notifications')
    ]
]);
?>
<?php $this->start('body') ?>
<h1><?= __('Notifications') ?></h1>
<hr />
<div class="row">
    <div class="col-md-12">
        <div class="float-end">
            <?= $this->listing('ul', ['class' => 'list-inline'])
                ->li(['link' => ['profile/notifications', ['all']], 'text' => __('All')], ['class' => 'list-inline-item'])
                ->li(['link' => ['profile/notifications', ['unread']], 'text' => __('Unread')], ['class' => 'list-inline-item'])
                ->display()
            ?>
        </div>
    </div>
</div>
<?php
if (!$model->items || count($model->items) < 1) {
    echo '<p class="alert alert-warning">' . __('No notifications available') . '</p>';
    $this->stop();
    return;
}
?>
<?php foreach ($model->items as $item): ?>
    <div class="notice<?= $item['new'] ? ' notice-new' : ''; ?>">
        <span class="badge badge-info"><?= $item['date'] ?></span> <?= $item['text'] ?>
    </div>
<?php endforeach; ?>

<?= $this->bootstrap()->pagination(['profile/notifications'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display()
?>
<?php $this->stop() ?>
