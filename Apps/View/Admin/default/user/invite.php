<?php

/** @var $model Apps\Model\Admin\User\FormInviteSend */
/** @var $this object */
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Send invite');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('user/index') => __('User list'),
    __('Send invite')
];
?>

<?= $this->show('user/_tabs') ?>
    <h1><?= $this->title ?></h1>
    <hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?= $form->field('email', 'email', ['class' => 'form-control'], __('Specify user email')) ?>

    <div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Send'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>