<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Comments\FormCommentUpdate $model */
/** @var string $type */

$this->layout('_layouts/default', [
    'title' => __('Edit comment')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Edit comment/answer') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Widgets') => ['widget/index'],
    __('Comments') => ['comments/index'],
    __('Edit comment or answer')
]]) ?>

<?= $this->insert('comments/_tabs'); ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->fieldset()->text('guestName', null, __('Guest name for this comment or answer if defined')) ?>
<?= $form->fieldset()->textarea('message', ['class' => 'form-control wysiwyg', 'html' => true], __('Comment message text')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary'])?>

<?= Url::a(['comments/read', [$model->getCommentId()]], __('Back'), ['class' => 'btn btn-light'])?>

<?= $form->stop() ?>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>
<?= \Widgets\Tinymce\Tinymce::widget([
    'config' => 'small'
]); ?>
<?php $this->stop() ?>
