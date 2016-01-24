<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var \Apps\Model\Admin\Comments\FormSettings $model */

$this->title = __('Comments settings');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    Url::to('comments/index') => __('Comments'),
    __('Settings')
];

?>

<?= $this->render('comments/_tabs') ?>

<h1><?= __('Comments settings') ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']) ?>

<?= $form->start() ?>

<?= $form->field('perPage', 'text', ['class' => 'form-control'], __('Comments count to display per one page')) ?>
<?= $form->field('delay', 'text', ['class' => 'form-control'], __('Delay between 2 comment posts or answers from one user (in seconds)')) ?>
<?= $form->field('editTime', 'text', ['class' => 'form-control'], __('Time after comment add when user can edit it own comment (in seconds)')) ?>
<?= $form->field('minLength', 'text', ['class' => 'form-control'], __('Minimal comment length to be valid')) ?>
<?= $form->field('maxLength', 'text', ['class' => 'form-control'], __('Maximum comment length to be valid')) ?>

<?= $form->field('guestAdd', 'checkbox', null, __('Allow add comments for not authorized users?')) ?>

<?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->finish() ?>
