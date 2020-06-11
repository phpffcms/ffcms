<?php

use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Content\FormCategoryUpdate $model */

$this->layout('_layouts/default', [
    'title' => __('Category manage')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Category manage') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Contents') => ['content/index'],
    __('Categories') => ['content/categories'],
    __('Category manage')
]]) ?>

<?= $this->insert('content/_tabs') ?>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?php
$menu = $this->bootstrap()->nav('ul', ['class' => 'nav-tabs']);
foreach (\App::$Properties->get('languages') as $lang) {
    $menu->menu([
        'text' => Str::upperCase($lang),
        'tab' => function() use ($form, $lang) {
            return $form->fieldset()->text('title.' . $lang, null, __('Enter category title, visible for users')) .
                $form->fieldset()->text('description.' . $lang, null, __('Enter category description'));
        },
        'tabActive' => $lang === \App::$Request->getLanguage()
    ]);
}
?>

<div class="nav-border">
    <?= $menu->display() ?>
</div>

<?php
if ($model->id === 1) { // general category (root) - no remove/rename/etc sh@t
    echo $form->fieldset()->text('path', ['disabled' => null], __('Enter category path slug for URI building'));
} else {
    echo $form->fieldset()->select('dependId', ['options' => $model->categoryList(), 'optionsKey' => true]);
    echo $form->fieldset()->text('path', null, __('Enter category path slug for URI building'));
}
?>

<?= $form->fieldset()->boolean('configs.showDate', null, __('Display dates of content in this category?')) ?>
<?= $form->fieldset()->boolean('configs.showRating', null, __('Display rating for items in this category?'))?>
<?= $form->fieldset()->boolean('configs.showCategory', null, __('Display current category for content?')) ?>
<?= $form->fieldset()->boolean('configs.showSimilar', null, __('Show the similar content items for this category? This option introduce additional system load and memory usage.')) ?>
<?= $form->fieldset()->boolean('configs.showAuthor', null, __('Display information about content authors in this category?')) ?>
<?= $form->fieldset()->boolean('configs.showViews', null, __('Display information about content view count in this category?')) ?>
<?= $form->fieldset()->boolean('configs.showComments', null, __('Display comment list and comment form in this category?')) ?>
<?= $form->fieldset()->boolean('configs.showPoster', null, __('Display content poster from gallery in this category?')) ?>
<?= $form->fieldset()->boolean('configs.showTags', null, __('Display tag list, based on keywords data?')) ?>
<?= $form->fieldset()->boolean('configs.showRss', null, __('Allow display RSS 2.0 feed for this category?')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-secondary', 'link' => ['content/categories']]) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
