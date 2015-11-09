<?php
use Ffcms\Core\Helper\Type\Obj;

/** @var $notify array */
/** @var $useCaptcha bool */
/** @var $model \Apps\Model\Front\User\FormRegister */
/** @var $this \Ffcms\Core\Arch\View */
$this->title = __('Sign up');
?>

<h1 class="text-center"><?php echo __('Sign up'); ?></h1>
<?= $this->render('user/_tabs'); ?>

<br/>

<?php

// mark form elements if it wrong
if (Obj::isArray($model->getWrongFields()) && count($model->getWrongFields()) > 0) {
    foreach ($model->getWrongFields() as $fieldName) {
        $fieldId = $model->getFormName() . '-' . $fieldName;
        \App::$Alias->addPlainCode('js', '$("#' . $fieldId . '").parent().parent(".form-group").addClass("has-error");');
    }
}
?>

<?php $form = new \Ffcms\Core\Helper\HTML\Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?php
echo $form->field('login', 'text', ['class' => 'form-control'], __('Enter your username for future use on the site'));
echo $form->field('email', 'text', ['class' => 'form-control'], __('Enter your e-mail for an account'));
echo $form->field('password', 'password', ['class' => 'form-control'], __('Enter a password for your account. It should be longer than 3 characters'));
echo $form->field('repassword', 'password', ['class' => 'form-control'], __('Repeat your password to be sure it correct'));
if (true === $useCaptcha) {
    if (\App::$Captcha->isFull()) {
        echo '<div class="col-md-offset-3 col-md-9">' . \App::$Captcha->get() . '</div>';
    } else {
        echo $form->field('captcha', 'captcha', ['class' => 'form-control'], __('Enter data from security image to prove that you are human. If you can\'t read symbols - click on image to reload'));
    }
}
?>

<div class="col-md-9 col-md-offset-3"><?php echo $form->submitButton(__('Register!'), ['class' => 'btn btn-default']); ?></div>


<?= $form->finish(); ?>