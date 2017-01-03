<?php

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var $this \Ffcms\Core\Arch\View */
/** @var $model \Apps\Model\Front\Content\FormNarrowContentUpdate */

echo Ffcms\Widgets\Ckeditor\Ckeditor::widget(['targetClass' => 'wysiwyg', 'config' => 'config-medium']);

$this->title = __('Content update');

$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    Url::to('content/index') => __('Contents'),
    Url::to('content/my') => __('My content'),
    __('Content edit')
];

?>

<h1><?= __('Content edit')?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) ?>
<?= $form->start() ?>

<?php
$items = [];
foreach (\App::$Properties->get('languages') as $lang) {
    $items[] = [
        'type' => 'tab',
        'text' => __('Lang') . ': ' . Str::upperCase($lang),
        'content' =>
            $form->field('title.' . $lang, 'text', ['class' => 'form-control'], __('Please, enter the title of your material for current language locale')) .
            $form->field('text.' . $lang, 'textarea', ['class' => 'form-control wysiwyg', 'rows' => 7, 'html' => true]),
        'html' => true,
        'active' => $lang === \App::$Request->getLanguage(),
        '!secure' => true
    ];
}

echo Nav::display([
    'property' => ['class' => 'nav-pills'],
    'blockProperty' => ['class' => 'nav-locale-block'],
    'tabAnchor' => 'content-update-general-locale',
    'items' => $items
]);
?>

<?= $form->field('path', 'text', ['class' => 'form-control'], __('Set path slug for content item. Allowed items: a-z, 0-9, -')); ?>
<?= $form->field('categoryId', 'select', ['class' => 'form-control', 'size' => 4, 'options' => ContentCategory::getSortedCategories(), 'optionsKey' => true], __('Select content category')); ?>

<?= $form->field('poster', 'file', null, __('Select poster image for this content')) ?>

<div class="col-md-offset-3 col-md-9">
    <?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>
</div>

<?= $form->finish() ?>

<script>
$(document).ready(function(){
    CKEDITOR.disableAutoInline = true;
});
</script>