<?php

/** @var $model Apps\Model\Admin\UserDeleteForm */
/** @var $this object */
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Delete user');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('user/index') => __('User list'),
    __('Delete user')
];

?>

<?= $this->show('user/_tabs') ?>

<h1><?= __('Delete user') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?= $form->field('email', 'inputText', ['class' => 'form-control', 'disabled' => null]) ?>
<?= $form->field('login', 'inputText', ['class' => 'form-control', 'disabled' => null]) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Delete'), ['class' => 'btn btn-danger']) ?></div>

<?= $form->finish() ?>