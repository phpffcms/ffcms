<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var \Apps\Model\Admin\Feedback\FormUpdate $model */

$this->title = __('Feedback update');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('feedback/index') => __('Feedback'),
    __('Update feedback')
];

echo $this->render('feedback/_tabs');
?>

<h1><?= __('Feedback edit') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']) ?>

<?= $form->start() ?>

<?= $form->field('name', 'text', ['class' => 'form-control'], __('Author name for this item')) ?>
<?= $form->field('email', 'email', ['class' => 'form-control'], __('Author email for this item')) ?>
<?= $form->field('message', 'textarea', ['class' => 'form-control'], __('Message text')) ?>

<div class="col-md-offset-3 col-md-9">
    <?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>
</div>

<?= $form->finish() ?>
