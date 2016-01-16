<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Content\FormSettings */

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Settings');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/index') => __('Contents'),
    __('Settings')
];

?>

<?= $this->render('content/_tabs') ?>

<h1><?= __('Content settings') ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '']); ?>

<?= $form->start() ?>

<?= $form->field('itemPerCategory', 'text', ['class' => 'form-control'], __('Count of content items per each page of category')) ?>
<?= $form->field('userAdd', 'checkbox', null, __('Allow users add content pages?')) ?>
<?= $form->field('multiCategories', 'checkbox', null, __('Display content from child categories?')) ?>
<?= $form->field('galleryResize', 'text', ['class' => 'form-control'], __('Specify maximum size of image in gallery in px')) ?>
<?= $form->field('gallerySize', 'text', ['class' => 'form-control'], __('Specify maximum image size in gallery in kb. Example: 500 = 0,5 mb')) ?>
<?= $form->field('keywordsAsTags', 'checkbox', null, __('Display tag list, based on keywords data?')) ?>
<?= $form->field('rss', 'checkbox', null, __('Display rss line of content add, changes, updates?')) ?>
<?= $form->field('rssFull', 'checkbox', null, __('Display full text of content in rss line?')) ?>

<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>