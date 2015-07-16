<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Content\FormContentDelete */

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Content delete');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/index') => __('Contents'),
    __('Content delete')
];

?>

<?= $this->show('content/_tabs') ?>

<h1><?= $this->title ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal']) ?>

<?= $form->field('id', 'text', ['class' => 'form-control', 'disabled' => '']) ?>
<?= $form->field('title', 'text', ['class' => 'form-control', 'disabled' => '']) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Remove'), ['class' => 'btn btn-danger']) ?></div>

<?= $form->finish() ?>