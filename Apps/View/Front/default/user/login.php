<?php
/** @var $useCaptcha bool */
/** @var $notify array */
/** @var $model \Apps\Model\Front\User\FormLogin */

/** @var $this \Ffcms\Core\Arch\View */
$this->title = __('Log In');
?>
<h1 class="text-center"><?php echo $this->title; ?></h1>
<?= $this->render('user/_tabs'); ?>

<br/>

<?php $form = new \Ffcms\Core\Helper\HTML\Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?= $form->field('login', 'text', ['class' => 'form-control'], __('Input you login or email')); ?>
<?= $form->field('password', 'password', ['class' => 'form-control'], __('Input you password')); ?>

<?php
if (true === $useCaptcha) {
    if (\App::$Captcha->isFull()) {
        echo '<div class="col-md-offset-3 col-md-9">' . \App::$Captcha->get() . '</div>';
    } else {
        echo $form->field('captcha', 'captcha', ['class' => 'form-control'], __('Enter data from security image to prove that you are human. If you can\'t read symbols - click on image to reload'));
    }
}
?>

<div class="col-md-9 col-md-offset-3"><?php echo $form->submitButton(__('Do Login'), ['class' => 'btn btn-default']); ?></div>


<?= $form->finish(); ?>