<?php
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $model \Apps\Model\Front\User\FormPasswordChange */
/** @var $this \Ffcms\Core\Arch\View */
$this->title = __('Password recovery');
?>

<h1><?= __('Password recovery'); ?></h1>
<hr />
<?= __('You require password recovery. Now you can set new password for your account') ?>
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']); ?>

<?= $form->start() ?>

<?= $form->field('password', 'password', ['class' => 'form-control'], __('Set new password for account')) ?>
<?= $form->field('repassword', 'password', ['class' => 'form-control'], __('Repeat new password')) ?>

<?php
if (\App::$Captcha->isFull()) {
    echo '<div class="col-md-offset-3 col-md-9">' . \App::$Captcha->get() . '</div>';
} else {
    echo $form->field('captcha', 'captcha', ['class' => 'form-control'], __('Enter data from security image to prove that you are human. If you can\'t read symbols - click on image to reload'));
}
?>

<div class="col-md-9 col-md-offset-3">
    <?= $form->submitButton(__('Change'), ['class' => 'btn btn-primary']) ?>
</div>

<?= $form->finish() ?>


