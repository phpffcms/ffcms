<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Admin\User\FormUserDelete $model */
/** @var Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Delete users'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('user/index') => __('User list'),
        __('Delete users')
    ]
]);

?>
<?php $this->start('body') ?>
<?= $this->insert('user/_tabs') ?>

<h1><?= __('Delete users') ?></h1>
<p><?= __('Are you sure to delete this users?') ?></p>
<?php
$table = $this->table(['class' => 'table'])
    ->head([
        ['text' => '#'],
        ['text' => __('Email')],
        ['text' => __('Nickname')],
        ['text' => __('Register date')]
    ]);

foreach ($model->users as $user) {
    /** @var \Apps\ActiveRecord\User $user */
    $nickname = \Ffcms\Core\Helper\Simplify::parseUserNick($user->id);
    $table->row([
        ['text' => $user->id],
        ['text' => $user->email],
        ['text' => $nickname],
        ['text' => Date::convertToDatetime($user->created_at, Date::FORMAT_TO_HOUR)]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?php $form = $this->form($model); ?>
<?= $form->start() ?>

<div class="row mb-2 mt-2">
    <div class="col-12">
        <?= $form->field()->boolean('delete', ['id' => 'delete_user_data']) ?>
        <label for="delete_user_data"><?= __('Delete all user data (comments, content, wall posts, feedback)?') ?></label>
    </div>
</div>

<?= $form->button()->submit(__('Delete'), ['class' => 'btn btn-danger']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['user/index'], 'class' => 'btn btn-secondary']) ?>
<?= $form->stop() ?>

<?php $this->stop() ?>
