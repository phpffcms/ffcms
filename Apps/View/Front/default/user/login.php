<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var bool $useCaptcha */
/** @var \Apps\Model\Front\User\FormLogin $model */
/** @var \Ffcms\Core\Arch\View $this  */
/** @var string $redirect */

$this->layout('_layouts/default', [
    'title' => __('Log In')
])
?>

<?php $this->start('body') ?>

<h1><?= __('Log In') ?></h1>
<?= $this->insert('user/_menu/tabs') ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('login', null, __('Input you login or email')) ?>

<?= $form->stop() ?>

<?php $this->end() ?>