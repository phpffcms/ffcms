<?php

use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\User $user */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Front\Profile\FormAvatarUpload $model */

$this->layout('_layouts/default', [
    'title' => __('Avatar change'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        Url::to('profile/show', [\App::$User->identity()->getId()]) => __('Profile'),
        __('Avatar settings')
    ]
]);
?>

<?php $this->start('body') ?>

<?php $this->insert('profile/menus/settings') ?>

<h1><?= __('Avatar settings') ?></h1>
<hr />

<?php $form = $this->form($model, ['enctype' => 'multipart/form-data', 'method' => 'POST']) ?>

<?= $form->start() ?>

<?= $form->fieldset()->file('file', null, __('Select jpg, png or gif avatar')) ?>
<?= $form->button()->submit(__('Change'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
