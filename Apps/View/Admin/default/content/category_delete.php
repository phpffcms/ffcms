<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Content\FormCategoryDelete */

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Category delete');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/category') => __('Categories'),
    __('Category delete')
];

?>

<?= $this->render('content/_tabs') ?>

<h1><?= __('Category delete') ?></h1>
<hr />
<p><?= __('Are you sure to delete this category and all depended categories of this') ?>? <?= __('All content will be moved to category acceptor') ?></p>
<?php $form = new Form($model, ['class' => 'form-horizontal']) ?>

<?= $form->start() ?>

<?= $form->field('title', 'text', ['class' => 'form-control', 'disabled' => '']); ?>
<?= $form->field('path', 'text', ['class' => 'form-control', 'disabled' => '']); ?>

<?= $form->field('moveTo', 'select', ['class' => 'form-control', 'options' => $model->categoryList(), 'optionsKey' => true], __('Select category acceptor of current category contents')) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Remove'), ['class' => 'btn btn-danger']) ?></div>

<?= $form->finish() ?>