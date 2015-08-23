<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Content\FormCategoryUpdate */

use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Type\String;
use Ffcms\Core\Helper\Url;

$this->title = __('Category manage');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/categories') => __('Categories'),
    __('Category manage')
];

?>

<?= $this->render('content/_tabs') ?>

<h1><?= __('Category manage') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '']) ?>

<?php
$items = [];
foreach (\App::$Property->get('languages') as $lang) {
    $items[] = [
        'type' => 'tab',
        'text' => __('Lang') . ': ' . String::upperCase($lang),
        'content' => $form->field('title.' . $lang, 'text', ['class' => 'form-control'], __('Enter category title, visible for users')) .
            $form->field('description.' . $lang, 'text', ['class' => 'form-control'], __('Enter category description')),
        'html' => true,
        'active' => $lang === \App::$Request->getLanguage(),
        '!secure' => true
    ];
}
?>

<?= Nav::display([
    'property' => ['class' => 'nav-pills'],
    'blockProperty' => ['class' => 'nav-locale-block'],
    'tabAnchor' => 'category-update-locale',
    'items' => $items
]) ?>

<?php
$pathProperty = [
    'class' => 'form-control'
];
if ((int)$model->id == '1') {
    $pathProperty['disabled'] = '';
} else {
    echo $form->field('dependId', 'select', ['class' => 'form-control', 'options' => $model->categoryList(), 'optionsKey' => true], __('Select owner category for this category'));
}
?>

<?= $form->field('path', 'text', $pathProperty, __('Enter category path slug for URI building')) ?>
<?= $form->field('configs.showDate', 'checkbox', null, __('Display dates of content in this category?')) ?>
<?= $form->field('configs.showCategory', 'checkbox', null, __('Display current category for content?')) ?>
<?= $form->field('configs.showAuthor', 'checkbox', null, __('Display information about content authors in this category?')) ?>
<?= $form->field('configs.showViews', 'checkbox', null, __('Display information about content view count in this category?')) ?>
<?= $form->field('configs.showComments', 'checkbox', null, __('Display comment list and comment form in this category?')) ?>
<?= $form->field('configs.showPoster', 'checkbox', null, __('Display content poster from gallery in this category?')) ?>

<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>