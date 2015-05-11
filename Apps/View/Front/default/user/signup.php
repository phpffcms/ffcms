<?php
/** @var $model \Apps\Model\Front\RegisterForm */
/** @var $this \Ffcms\Core\Arch\View */
$this->title = __('Sign up');
?>

<h1 class="text-center"><?php echo __('Sign up'); ?></h1>
<?php echo $this->show('user/_tabs'); ?>

<br/>

<?php
/**$notify = \App::$Message->getGroup(['user/signup', 'global']);

if ($notify !== null) {
    echo $this->show('macro/notify', ['object' => $notify]);
}*/

?>



<?php $form = new \Ffcms\Core\Helper\HTML\Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?php
echo $form->field('login', 'inputText', ['class' => 'form-control'], __('Input your login for website'));
echo $form->field('email', 'inputText', ['class' => 'form-control'], __('Input your email for validation'));
echo $form->field('password', 'inputPassword', ['class' => 'form-control'], __('Input you password'));
echo $form->field('repassword', 'inputPassword', ['class' => 'form-control'], __('Repeat your password to be sure it correct'));
?>

<div class="col-md-9 col-md-offset-3"><?php echo $form->submitButton(__('Register!'), ['class' => 'btn btn-default']); ?></div>


<?php $form->finish(); ?>