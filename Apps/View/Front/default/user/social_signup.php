<?php
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $model \Apps\Model\Front\User\FormSocialAuth */
/** @var $this \Ffcms\Core\Arch\View */
$this->title = __('Social login');
?>

<h1 class="text-center"><?= __('Social login'); ?></h1>
<hr />
<p class="alert alert-warning"><?= __('This social account: %profile% used first time in our website. Please, complete form below', ['profile' => $model->profileLink]) ?></p>
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?= $form->start() ?>

<?= $form->field('login', 'text', ['class' => 'form-control'], __('Enter your username for future use on the site')); ?>
<?= $form->field('email', 'text', ['class' => 'form-control'], __('Enter your e-mail for an account')); ?>
<?= $form->field('password', 'password', ['class' => 'form-control'], __('Enter a password for your account. It should be longer than 3 characters')); ?>
<?= $form->field('repassword', 'password', ['class' => 'form-control'], __('Repeat your password to be sure it correct')); ?>

<div class="col-md-9 col-md-offset-3">
    <?= $form->submitButton(__('Register and sign in'), ['class' => 'btn btn-primary']); ?>
    <?= Url::link(['user/login'], __('Cancel'), ['class' => 'btn btn-default']) ?>
</div>

<?= $form->finish(); ?>