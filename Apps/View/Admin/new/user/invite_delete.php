<?php

/** @var \Ffcms\Templex\Template\Template $this */

use Ffcms\Templex\Url\Url;

/** @var \Apps\Model\Admin\User\FormInviteDelete $model */
/** @var \Apps\ActiveRecord\Invite|\Illuminate\Support\Collection $record */

$this->layout('_layouts/default', [
    'title' => __('Delete invite'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('user/index') => __('Invitation list'),
        __('Delete invite')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Delete invite') ?></h1>
<p><?= __('Are you sure to delete invite: %mail%', ['mail' => $record->email]) ?></p>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->button()->submit(__('Delete'), ['class' => 'btn btn-danger']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['user/invitelist'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>
<?php $this->stop() ?>

