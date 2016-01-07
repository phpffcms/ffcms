<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;
/** @var $model Apps\Model\Front\Profile\FormPasswordChange */
/** @var $this object */

$this->title = __('Change password');

$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    Url::to('profile/show', \App::$User->identity()->getId()) => __('Profile'),
    __('Password')
];

?>

<?= $this->render('profile/_settingsTab') ?>

<h1><?= __('Change password') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '', 'method' => 'post']); ?>

<?= $form->start() ?>

<?= $form->field('current', 'password', ['class' => 'form-control'], __('Enter your current account password')) ?>
<?= $form->field('new', 'password', ['class' => 'form-control'], __('Enter new password for account')) ?>
<?= $form->field('renew', 'password', ['class' => 'form-control'], __('Repeat new password for account')) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Update'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>