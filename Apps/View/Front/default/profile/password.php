<?php

use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Front\Profile\FormPasswordChange $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Change password'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        Url::to('profile/show', [\App::$User->identity()->getId()]) => __('Profile'),
        __('Password')
    ]
]);

?>

<?php $this->start('body') ?>

<?php $this->insert('profile/menus/settings') ?>

<h1><?= __('Change password') ?></h1>
<hr />
<?php $form = $this->form($model); ?>
<?= $form->start() ?>

<?= $form->fieldset()->password('current', null, __('Enter your current account password')) ?>
<?= $form->fieldset()->password('new', null, __('Enter new password for account: 8 or more symbols, chars & numbers, at least one uppercase symbol')) ?>
<?= $form->fieldset()->password('renew', null, __('Repeat new password for account')) ?>

<?= $form->button()->submit(__('Update'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>
<script>
$(document).ready(function(){
    $('input[id*="new"]').on("focusout", function(){
        validator_pwd($(this).val()) ? $(this).removeClass("bg-danger") : $(this).addClass("bg-danger"); 
    });
});  
</script>
<?php $this->stop() ?>