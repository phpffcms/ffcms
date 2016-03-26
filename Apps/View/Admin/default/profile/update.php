<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $model Apps\Model\Front\Profile\FormSettings */
/** @var $this object */
/** @var $user object */
/** @var $profile object */

$this->title = __('Profile edit');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('profile/index') => __('Profile list'),
    __('Profile edit')
];

?>

<?= $this->render('profile/_tabs') ?>

<h1><?= __('Edit user profile') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '', 'method' => 'post']) ?>

<?= $form->start() ?>

<?= $form->field('nick', 'text', ['class' => 'form-control']) ?>
<?= $form->field('sex', 'select', ['class' => 'form-control', 'options' => ['0' => __('Unknown'), '1' => __('Male'), '2' => __('Female')], 'optionsKey' => true]) ?>
<?= $form->field('birthday', 'text', ['class' => 'form-control'], __('Birthday date in d.m.Y format')) ?>
<?= $form->field('city', 'text', ['class' => 'form-control']) ?>
<?= $form->field('hobby', 'text', ['class' => 'form-control']) ?>
<?= $form->field('phone', 'text', ['class' => 'form-control']) ?>
<?= $form->field('url', 'text', ['class' => 'form-control']) ?>

    <div class="row">
        <div class="col-md-3">
            <div class="text-right"><strong><?= __('Account data') ?></strong></div>
        </div>
        <div class="col-md-9">
            <?= Url::link(['user/update', $user->id], __('Edit account data')); ?>
        </div>
    </div>
    <br />
    <div class="col-md-9 col-md-offset-3">
        <?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>
    </div>

<?= $form->finish() ?>