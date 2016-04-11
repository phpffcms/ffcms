<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var \Apps\Model\Admin\Newcontent\FormSettings $model */
/** @var \Ffcms\Core\Arch\View $this */

$this->title = __('New content');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('newcontent/index') => __('New content'),
    __('Settings')
];

?>

<h1><?= __('New content') ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']) ?>

<?= $form->start() ?>

<?= $form->field('categories', 'multiselect', ['class' => 'form-control', 'options' => $model->getCategories(), 'optionsKey' => true, 'size' => 4], __('Select categories of wich content will be selected')) ?>
<?= $form->field('count', 'text', ['class' => 'form-control'], __('How many content items would be displayed in block?'))?>
<?= $form->field('cache', 'text', ['class' => 'form-control'], __('Widget default cache time in seconds. Set 0 to disable caching.'))?>

<?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->finish() ?>
