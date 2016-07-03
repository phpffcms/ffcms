<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $routes array */
/** @var $model \Apps\Model\Admin\Main\FormAddRoute */

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('main/routing') => __('Routing'),
    __('Add route')
];

$this->title = __('Add route');

?>

<h1><?= __('New route') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']); ?>
<?= $form->start() ?>

<?= $form->field('type', 'select',
    [
        'class' => 'form-control',
        'options' => [
            'Alias' => __('Static (alias) route'),
            'Callback' => __('Dynamic (callback) route')
        ],
        'optionsKey' => true
    ],
    __('Specify type of defined rule')) ?>
<?= $form->field('loader', 'select', ['options' => ['Front', 'Admin', 'Api'], 'class' => 'form-control'], __('Select loader type where be applied rule')) ?>
<?= $form->field('source', 'text', ['class' => 'form-control'], __('Define source path (for static alias) or class name (for dynamic rule) to use it for target query')) ?>
<?= $form->field('target', 'text', ['class' => 'form-control'], __('Define target path or class path for displayd item on source path')) ?>


<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Add new route'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>
