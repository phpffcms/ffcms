<?php
use Ffcms\Core\Helper\Object;

/** @var $notify array */
/** @var $model \Apps\Model\Front\RegisterForm */
/** @var $this \Ffcms\Core\Arch\View */
$this->title = __('Sign up');
?>

<h1 class="text-center"><?php echo __('Sign up'); ?></h1>
<?php echo $this->show('user/_tabs'); ?>

<br/>

<?php
// show notification if exist
if (Object::isArray($notify) && count($notify) > 0) {
    echo $this->show('macro/notify', ['notify' => $notify]);
}

// mark form elements if it wrong
if (Object::isArray($model->getWrongFields()) && count($model->getWrongFields()) > 0) {
    foreach ($model->getWrongFields() as $fieldName) {
        $fieldId = $model->getFormName() . '-' . $fieldName;
        \App::$Alias->addPlainCode('js', '$("#' . $fieldId . '").parent().parent(".form-group").addClass("has-error");');
    }
}
?>



<?php $form = new \Ffcms\Core\Helper\HTML\Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?php
echo $form->field('login', 'inputText', ['class' => 'form-control'], __('Enter your username for future use on the site'));
echo $form->field('email', 'inputText', ['class' => 'form-control'], __('Enter your e-mail for an account'));
echo $form->field('password', 'inputPassword', ['class' => 'form-control'], __('Enter a password for your account. It should be longer than 3 characters'));
echo $form->field('repassword', 'inputPassword', ['class' => 'form-control'], __('Repeat your password to be sure it correct'));
?>

<div class="col-md-9 col-md-offset-3"><?php echo $form->submitButton(__('Register!'), ['class' => 'btn btn-default']); ?></div>


<?php $form->finish(); ?>