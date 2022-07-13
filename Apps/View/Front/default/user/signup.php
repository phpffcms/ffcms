<?php

use \Ffcms\Core\Helper\Type\Str;

/** @var array $notify */
/** @var bool $useCaptcha */
/** @var array $config */
/** @var \Apps\Model\Front\User\FormRegister $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Sign up')
])
?>

<?php $this->start('body') ?>

<h1><?= __('Sign up'); ?></h1>
<?= $this->insert('user/_menu/tabs') ?>

<?php if ($config['allowedEmails'] && !Str::likeEmpty($config['allowedEmails'])): ?>
    <?= $this->bootstrap()->alert('info', __('Attention! This website allows registration only from followed email zones: %emails%', ['emails' => $config['allowedEmails']])) ?>
<?php endif ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?php // $this->insert('user/_menu/_social_panel') ?>

<?= $form->fieldset()->text('email', null, __('Enter your e-mail for an account')); ?>
<?= $form->fieldset()->password('password', null, __('Enter a password for your account. It should be: longer than 8 chars, contains chars & numbers, contains at least 1 uppercase symbol')); ?>
<?= $form->fieldset()->password('repassword', null, __('Repeat your password to be sure it correct')); ?>

<?= $this->insert('_core/form/fieldset/captcha', ['form' => $form]) ?>

<?= $form->button()->submit(__('Register!'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?= $this->stop() ?>


<?php $this->push('javascript') ?>
<script>
$(document).ready(function(){
    $('input[id*="email"]').on("focusout", function(){
        validator_email($(this).val()) ? $(this).removeClass("bg-danger") : $(this).addClass("bg-danger"); 
    });

    $('input[id*="password"]').on("focusout", function(){
        validator_pwd($(this).val()) ? $(this).removeClass("bg-danger") : $(this).addClass("bg-danger"); 
    });
});  
</script>
<?php $this->stop() ?>