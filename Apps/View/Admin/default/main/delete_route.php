<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $model \Apps\Model\Admin\Main\EntityDeleteRoute */

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('main/routing') => __('Routing'),
    __('Deleting route')
];

$this->title = __('Deleting route');

?>

<h1><?= __('Deleting route') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>

<?= $form->field('type', 'text', ['disabled' => true, 'class' => 'form-control']) ?>
<?= $form->field('loader', 'text', ['disabled' => true, 'class' => 'form-control']) ?>
<?= $form->field('source', 'text', ['disabled' => true, 'class' => 'form-control']) ?>


<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Delete this route'), ['class' => 'btn btn-danger']) ?></div>

<?= $form->finish() ?>
