<?php

/** @var $model Apps\Model\Admin\User\FormUserUpdate */
/** @var $this object */
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Manage user');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('user/index') => __('User list'),
    __('Manage user')
];
?>

<?= $this->render('user/_tabs') ?>
<h1><?= $this->title ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>
<?= $form->start() ?>

<?= $form->field('email', 'email', ['class' => 'form-control'], __('Specify user email')) ?>
<?= $form->field('login', 'text', ['class' => 'form-control'], __('Specify user login')) ?>
<?= $form->field('newpassword', 'text', ['class' => 'form-control'], __('Specify new user password if you want to change it! Less empty field to save current')) ?>
<?= $form->field('role_id', 'select', ['class' => 'form-control', 'options' => $model->getRoleList(), 'optionsKey' => true]) ?>
<?= $form->field('approve_token', 'checkbox', null, __('Set if user is approved or not')) ?>

<?php if ($model->_user->getId() !== null): ?>
<div class="row">
    <div class="col-md-3">
        <div class="text-right"><strong><?= __('Profile data') ?></strong></div>
    </div>
    <div class="col-md-9">
        <?= Url::link(['profile/update', $model->_user->getProfile()->id], __('Edit profile data')); ?>
    </div>
</div>
<br />
<?php endif; ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>