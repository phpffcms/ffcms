<?php

/** @var $this \Ffcms\Core\Arch\View */
/** @var $model \Apps\Model\Front\User\FormRecovery */
use Ffcms\Core\Helper\HTML\Form;

$this->title = __('Recovery');

?>
<h1 class="text-center"><?= __('Recovery form') ?></h1>
<?= $this->render('user/_tabs'); ?>
<br/>

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?= $form->field('email', 'email', ['class' => 'form-control'], __('Input your account email')); ?>
<?php
if (\App::$Captcha->isFull()) {
    echo '<div class="col-md-offset-3 col-md-9">' . \App::$Captcha->get() . '</div>';
} else {
    echo $form->field('captcha', 'captcha', ['class' => 'form-control'], __('Enter data from security image to prove that you are human. If you can\'t read symbols - click on image to reload'));
}
?>

<div class="col-md-9 col-md-offset-3"><?php echo $form->submitButton(__('Make recovery'), ['class' => 'btn btn-default']); ?></div>

<?= $form->finish(); ?>