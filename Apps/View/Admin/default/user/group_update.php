<?php

/** @var $model Apps\Model\Admin\User\FormUserGroupUpdate */
/** @var $this object */
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Manage group');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('user/grouplist') => __('Group list'),
    __('Manage group')
];
?>

<?= $this->render('user/_tabs') ?>
    <h1><?= $this->title ?></h1>
    <hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?= $form->field('name', 'text', ['class' => 'form-control'], __('Set the name of the group')) ?>
<?= $form->field('permissions', 'checkboxes', ['options' => $model->getAllPermissions()]) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>