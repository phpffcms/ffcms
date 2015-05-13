<?php
use Ffcms\Core\Helper\Object;
/** @var $notify array */
/** @var $model \Apps\Model\Front\LoginForm */

/** @var $this \Ffcms\Core\Arch\View */
$this->title = __('Log In');
?>
<h1 class="text-center"><?php echo $this->title; ?></h1>
<?php echo $this->show('user/_tabs'); ?>

<br/>

<?php
if (Object::isArray($notify) && count($notify) > 0) {
    echo $this->show('macro/notify', ['notify' => $notify]);
}
?>

<?php $form = new \Ffcms\Core\Helper\HTML\Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?php echo $form->field('login', 'inputText', ['class' => 'form-control'], __('Input you login or email')); ?>
<?php echo $form->field('password', 'inputPassword', ['class' => 'form-control'], __('Input you password')); ?>

<div class="col-md-9 col-md-offset-3"><?php echo $form->submitButton(__('Do Login'), ['class' => 'btn btn-default']); ?></div>


<?php $form->finish(); ?>