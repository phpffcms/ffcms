<?php

/** @var Apps\Model\Admin\Content\FormContentUpdate $model */
/** @var \Ffcms\Templex\Template\Template $this */

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Templex\Helper\Html\Dom;
use Ffcms\Templex\Url\Url;
use Ffcms\Core\Helper\Type\Str;

$this->layout('_layouts/default', [
    'title' => __('Content edit'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('content/index') => __('Contents'),
        __('Content manage')
    ]
]);
//echo Ffcms\Widgets\Ckeditor\Ckeditor::widget(['targetClass' => 'wysiwyg', 'config' => 'config-full']);
?>

<?php $this->start('body') ?>

<?= $this->insert('content/_tabs') ?>

<h1><?= __('Content manage') ?></h1>
<?php
$form = $this->form($model);

echo $form->start();

$menu = $this->bootstrap()->nav('ul', ['class' => 'nav-tabs'])
    ->menu(['text' => __('General'), 'tab' => function() use ($form) {
        /** @var \Ffcms\Templex\Template\Template $this */
        $langMenu = $this->bootstrap()->nav('ul', ['class' => 'nav-tabs']);
        foreach (\App::$Properties->get('languages') as $lang) {
            $langMenu->menu([
                'text' => Str::upperCase($lang),
                'tab' => function() use ($form, $lang) {
                    return $form->fieldset()->text('title.' . $lang, null, __('Fill the title of the content for current language locale')) .
                        '<strong>' . __('Content text') . '</strong><br />' .
                        $form->field()->textarea('text.' . $lang, ['class' => 'form-control wysiwyg', 'rows' => 7]);
                },
                'tabActive' => $lang === \App::$Request->getLanguage()
            ]);
        }
        return (new Dom())->div(function() use ($langMenu) {
            return $langMenu->display();
            }, ['class' => 'nav-border']);
    }, 'tabActive' => true])
    ->menu(['text' => __('Properties'), 'tab' => function() use ($form){
        /** @var \Ffcms\Templex\Template\Template $this */
        $langMenu = $this->bootstrap()->nav('ul', ['class' => 'nav-tabs']);
        $context = $form->fieldset()->text('path', null, __('Slug of URL pathway for this content item'))
            . $form->fieldset()->select('categoryId', ['options' => ContentCategory::getSortedCategories(), 'optionsKey' => true, 'multiple' => null], __('Select content category'));

        foreach (\App::$Properties->get('languages') as $lang) {
            $langMenu->menu([
                'text' => Str::upperCase($lang),
                'tab' => function() use ($form, $lang) {
                    return $form->fieldset()->text('metaTitle.' . $lang, null, __('Set meta title for content page (displayed in browser head). Recommended length: 50-70 chars')).
                        $form->fieldset()->text('metaKeywords.' . $lang, null, __('Set meta keywords for this content (for search engine crawlers) separated by comma')).
                        $form->fieldset()->text('metaTitle.' . $lang, null, __('Set meta description for this content (for search engine crawlers). Recommended length: 200-250 chars'));
                },
                'tabActive' => $lang === \App::$Request->getLanguage()
            ]);
        }

        // compile context with lang menu
        $context .= $langMenu->display();

        return $context;
    }])
    ->menu(['text' => __('Gallery'), 'tab' => function() use ($form) {
        return '<div class="row" id="gallery-files"></div>
    <div class="row">
        <div class="col-md-8">
            <div class="dropzone dropzone-previews" id="ffcms-dropzone"></div>
        </div>
        <div class="col-md-4">
        ' . $form->fieldset()->select('poster', ['options' => [__('Not selected...')]], __("Select image from gallery as a poster for this content")) . '
        </div>
    </div><br/><br/>';
    }])
    ->menu(['text' => __('Other'), 'tab' => function() use ($form) {
        return $form->fieldset()->boolean('display', null, __('Can users view this content or only available for administrators?')) .
            $form->fieldset()->boolean('important', null, __('Make this material important and stack it on top of all news?')) .
            $form->fieldset()->text('createdAt', ['class' => 'form-control datapick'], __('Set the date of creation or leave empty for current date')) .
            $form->fieldset()->text('authorId', null, __('Enter author user_id or leave empty to set current user as author')) .
            $form->fieldset()->text('source', null, __('Set source URL if this content is copied from another website')) .
            $form->fieldset()->text('addRating', null, __('Add or reduce this content rating. Example: 5 gives +5 to total rating, -5 gives -5 to total'));
    }]);
echo $menu->display();
echo $form->button()->submit(__('Save'), ['class' => 'btn btn-primary mt-2']);
?>

<?= $form->stop() ?>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>
<?php
echo \Widgets\Tinymce\Tinymce::widget([
    'config' => 'full'
]);

?>
<?php $this->stop() ?>
