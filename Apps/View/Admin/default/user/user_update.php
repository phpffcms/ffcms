<?php

use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Admin\User\FormUserUpdate $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Manage user'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('user/index') => __('User list'),
        __('Manage user')
    ]
]);
?>

<?php $this->start('body') ?>
<?= $this->insert('user/_tabs') ?>

<h1><?= __('Manage user') ?></h1>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('email', null, __('Specify user email')) ?>
<?= $form->fieldset()->text('newpassword', null, __('Specify new user password if you want to change it! Less empty field to save current')) ?>
<?= $form->fieldset()->select('role_id', ['options' => $model->getRoleList(), 'optionsKey' => true]) ?>
<?= $form->field()->hidden('approve_token') ?>
<?= $form->fieldset()->boolean('approved', null, __('Set if user is approved or not')) ?>

<?php if ($model->_user->getId() !== null): ?>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="text-right"><strong><?= __('Ban user') ?></strong></div>
        </div>
        <div class="col-md-9">
            <?= Url::a(['main/banupdate', [], ['user' => $model->_user->id]], __('Ban access'), ['target' => '_blank', 'class' => 'text-danger']) ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="text-right"><strong><?= __('Profile preview') ?></strong></div>
        </div>
        <div class="col-md-9">
            <a href="<?= \App::$Alias->scriptUrl . '/profile/show/' . $model->_user->id ?>" target="_blank"><?= __('View profile on website') ?></a>
        </div>
    </div>
    <div class="row mt-3 mb-3">
        <div class="col-md-3">
            <div class="text-right"><strong><?= __('Profile data') ?></strong></div>
        </div>
        <div class="col-md-9">
            <?= Url::a(['profile/update', [$model->_user->profile->id]], __('Edit profile data')); ?>
        </div>
    </div>
<?php endif; ?>
<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-secondary', 'link' => ['user/index']]) ?>
<?= Url::a(['user/clear', [$model->_user->id]], __('Clear'), ['class' => 'btn btn-warning']) ?>&nbsp;
<?= Url::a(['user/delete', [$model->_user->id]], __('Delete'), ['class' => 'btn btn-danger']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
