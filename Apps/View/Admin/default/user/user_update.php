<?php

/** @var $model Apps\Model\Admin\UserUpdateForm */
/** @var $this object */
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Edit user');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('user/index') => __('User list'),
    __('Edit user')
];
?>

<?= $this->show('user/_tabs') ?>
<h1><?= $this->title ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?= $form->field('email', 'inputEmail', ['class' => 'form-control'], __('Specify user email')) ?>
<?= $form->field('login', 'inputText', ['class' => 'form-control'], __('Specify user login')) ?>
<?= $form->field('nick', 'inputText', ['class' => 'form-control'], __('Specify user nickname')) ?>
<?= $form->field('newpassword', 'inputText', ['class' => 'form-control'], __('Specify new user password if you want to change it! Less empty field to save current')) ?>
<?= $form->field('role_id', 'select', ['class' => 'form-control', 'options' => $model->getRoleList(), 'optionsKey' => true]) ?>
<?= $form->field('is_aproved', 'checkbox', null, __('Set if user is approved or not')) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>