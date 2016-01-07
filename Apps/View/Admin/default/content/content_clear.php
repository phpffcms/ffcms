<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Content\FormContentClear */

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Cleanup trash');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/index') => __('Contents'),
    __('Cleanup trash')
];

?>

<?= $this->render('content/_tabs') ?>

<h1><?= $this->title ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal']) ?>

<?= $form->start() ?>

<?= $form->field('count', 'text', ['class' => 'form-control', 'disabled' => ''], __('Count of content items to total remove')) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Total remove'), ['class' => 'btn btn-danger']) ?></div>

<?= $form->finish() ?>