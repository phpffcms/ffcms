<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\User\FormUserClear $model */

$this->layout('_layouts/default', [
    'title' => __('Manage user')
]);

?>

<?php $this->start('body') ?>

<h1><?= __('User cleanup') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Users') => ['user/index'],
    __('User cleanup')
]]) ?>

<?= $this->insert('user/_tabs') ?>

<div class="table-responsive">
    <?= $this->table(['class' => 'table'])
        ->head([
            ['text' => '#'],
            ['text' => __('Email')],
            ['text' => __('Full name')],
            ['text' => __('Register date')]
        ])->row([
            ['text' => $model->getUser()->id],
            ['text' => $model->getUser()->email],
            ['text' => $model->getUser()->profile->name ?? 'id' . $model->getUser()->id],
            ['text' => Date::convertToDatetime($model->getUser()->created_at, Date::FORMAT_TO_HOUR)],
        ])->display() ?>
</div>
<p><?= __('On this page you can make full cleanup user input data') ?></p>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->boolean('comments', null, __('Delete all user comments and answers?')) ?>
<?= $form->fieldset()->boolean('content', null, __('Delete all user content pages?')) ?>
<?= $form->fieldset()->boolean('feedback', null, __('Delete all user feedback requests?')) ?>
<?= $form->fieldset()->boolean('wall', null, __('Delete all wall posts and answers?')) ?>


<?= $form->button()->submit(__('Clear'), ['class' => 'btn btn-warning']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-light', 'link' => ['user/index']]) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
