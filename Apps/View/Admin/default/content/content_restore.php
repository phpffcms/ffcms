<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Content\FormContentRestore */

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Content restore');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/index') => __('Contents'),
    __('Content restore')
];

?>

<?= $this->show('content/_tabs') ?>

<h1><?= $this->title ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal']) ?>

<?= $form->field('id', 'text', ['class' => 'form-control', 'disabled' => 'disabled']) ?>
<?= $form->field('title', 'text', ['class' => 'form-control', 'disabled' => 'disabled']) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Restore'), ['class' => 'btn btn-success']) ?></div>

<?= $form->finish() ?>