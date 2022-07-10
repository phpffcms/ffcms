<?php

/** @var array $notify */
/** @var bool $useCaptcha */
/** @var \Apps\Model\Front\User\FormRegister $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Sign up')
])
?>

<?php $this->start('body') ?>

<h1><?= __('Sign up'); ?></h1>
<?= $this->insert('user/_menu/tabs') ?>

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
<script src="/vendor/phpffcms/ffcms-assets/node_modules/just-validate/dist/just-validate.production.min.js"></script>
<script>
    let validation_status = false;
    const form_name = '<?= $model->getFormName() ?>' 
    const jv = new window.JustValidate('#' + form_name, {
        errorFieldCssClass: 'is-invalid',
        errorLabelCssClass: 'is-label-invalid',
        lockForm: true,
        tooltip: {
            position: 'top',
        },
        errorContainer: '.errors-container',
    });
    jv.addField('#' + form_name + '-email', [
        {
            rule: 'email',
            errorMessage: '<?=__('Email has invalid format')?>'
        }
    ]).addField('#' + form_name + '-password', [
        {
            rule: 'password', 
            errorMessage: 'Password must contains at least 8 symbols, include chars & numbers and one uppercase symbol'
        }
    ]);

    // set trigger status global
    jv.onSuccess((event)=>{
        validation_status = true;
    }).onFail((event)=>{
        validation_status = false;
    });

</script>
<?php $this->stop() ?>