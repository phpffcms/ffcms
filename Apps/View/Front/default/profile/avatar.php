<?php
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $user \Apps\ActiveRecord\User */
/** @var $this object */
/** @var $model \Apps\Model\Front\Profile\FormAvatarUpload */

$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    Url::to('profile/show', $user->id) => __('Profile'),
    __('Avatar settings')
];

$this->title = __('Avatar change')
?>

<?= $this->show('profile/_settingsTab') ?>

<h1><?= $this->title; ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '', 'enctype' => 'multipart/form-data']); ?>

<?= $form->field('file', 'file', null, __('Select jpg, png or gif avatar')) ?>
<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Change'), ['class' => 'btn btn-warning']) ?></div>

<?= $form->finish() ?>