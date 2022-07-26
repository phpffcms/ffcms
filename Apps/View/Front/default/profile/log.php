<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\ActiveRecord\UserLog[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */

$this->layout('_layouts/default', [
    'title' => __('Logs'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        Url::to('profile/show', [\App::$User->identity()->getId()]) => __('Profile'),
        __('Logs')
    ]
]);
?>

<?php $this->start('body') ?>

<?php $this->insert('profile/menus/settings') ?>

<h2><?= __('My logs') ?></h2>
<hr />
<?php if(!$records || $records->count() < 1) {
    echo $this->bootstrap()->alert('info', __('No logs available'));
    $this->stop();
    return;
} ?>
<div class="table-responsive">
<?php
    $table = $this->table(['class' => 'table w-100'])
        ->head([
            ['text' => '#'],
            ['text' => __('Type')],
            ['text' => __('Message')],
            ['text' => __('Date')]
        ]);
    foreach ($records as $log) {
        $table->row([
            ['type' => 'text', 'text' => $log->id],
            ['type' => 'text', 'text' => $log->type],
            ['type' => 'text', 'text' => $log->message],
            ['type' => 'text', 'text' => Date::convertToDatetime($log->created_at, Date::FORMAT_TO_HOUR)]
        ]);
    }
    echo $table->display();
?>
</div>

<?= $this->bootstrap()->pagination(['profile/log'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display()
?>

<?php $this->stop() ?>