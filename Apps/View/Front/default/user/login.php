<?php
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var $useCaptcha bool */
/** @var $model \Apps\Model\Front\User\FormLogin */

/** @var $this \Ffcms\Core\Arch\View */
$this->title = __('Log In');
?>
<h1 class="text-center"><?= $this->title; ?></h1>
<?= $this->render('user/_tabs'); ?>

<br/>

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?= $form->start() ?>

<?= $this->render('user/_social_panel') ?>

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

<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Do Login'), ['class' => 'btn btn-default']); ?></div>


<?= $form->finish(); ?>