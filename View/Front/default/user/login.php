<?php
    /** @var $model \Model\Front\LoginForm */
    /** @var $this \Core\Arch\View */
    $this->title = __('Log In');
?>
<h1 class="text-center"><?php echo __('Log In'); ?></h1>
<?php echo $this->show('user/_tabs'); ?>

<br />

<?php $form = new \Core\Helper\HTML\Form([
    'model' => $model,
    'property' => ['class' => 'form-horizontal', 'method' => 'post', 'action' => ''],
    'name' => 'main-form',
    'structure' => '<div class="form-group"><label for="%name%" class="col-md-3 control-label">%label%</label><div class="col-md-9">%item% <p class="help-block">%help%</p></div></div>'
]); ?>

<?php echo $form->field('login', 'inputText', ['class' => 'form-control'], __('Input you login or email')); ?>
<?php echo $form->field('password', 'inputPassword', ['class' => 'form-control'], __('Input you password')); ?>
<div class="col-md-9 col-md-offset-3"><?php echo $form->submitButton(__('Do Login'), ['class' => 'btn btn-default']); ?></div>


<?php $form->finish(); ?>