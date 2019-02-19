<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Front\Profile\FormIgnoreAdd $model */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\ActiveRecord\Blacklist[] $records */
/** @var array $pagination */

$this->layout('_layouts/default', [
    'title' => __('Blacklist'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        Url::to('profile/show', [\App::$User->identity()->getId()]) => __('Profile'),
        __('Blacklist')
    ]
]);
?>

<?php $this->start('body') ?>

<?php $this->insert('profile/menus/settings') ?>

<h2><?= __('Add user ignore') ?></h2>
<hr />
<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('id', null, __('Enter id of user who you want to block')) ?>
<?= $form->fieldset()->text('comment', null, __('Remark memo about this block')) ?>

<?= $form->button()->submit(__('Block'), ['class' => 'btn btn-danger']) ?>

<?= $form->stop() ?>

<h2><?= __('List of blocked users') ?></h2>
<hr />
<?php if (!$records || $records->count() < 1) {
    echo '<p class="alert alert-info">' . __('No users in blacklist!') . '</p>';
    $this->stop();
    return;
} ?>

<div class="table-responsive">
    <?php
    $table = $this->table(['class' => 'table'])->head([
        ['text' => __('User')],
        ['text' => __('Comment')],
        ['text' => __('Add date')],
        ['text' => __('Actions')]
    ]);
    foreach ($records as $row) {
        $table->row([
            ['text' => Simplify::parseUserLink($row->target_id), 'html' => true],
            ['text' => $row->comment],
            ['text' => Date::convertToDatetime($row->created_at, Date::FORMAT_TO_DAY)],
            ['text' => Url::a(['profile/unblock', [$row->target_id]], '<i class="fas fa-trash-alt"></i>', ['html' => true]), 'html' => true]
        ]);
    }
    echo $table->display();
    ?>
<?= $this->bootstrap()->pagination(['profile/ignore'], ['class' => 'pagination justify-content-center'])
        ->size($pagination['total'], $pagination['page'], $pagination['step'])
        ->display(); ?>
</div>
<?php $this->stop() ?>
