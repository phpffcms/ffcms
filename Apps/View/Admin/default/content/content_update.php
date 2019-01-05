<?php

/** @var Apps\Model\Admin\Content\FormContentUpdate $model */
/** @var \Ffcms\Templex\Template\Template $this */

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Helper\Html\Dom;
use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Content edit'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('content/index') => __('Contents'),
        __('Content manage')
    ]
]);
?>

<?php $this->push('css') ?>
<!-- jquery ui plugin -->
<link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/jquery-ui-dist/jquery-ui.min.css" />
<!-- dropzone css plugin -->
<link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/dropzone/dist/dropzone.css" />
<style>
    .dropzone {
        border: 2px dashed #0087F7;
        border-radius: 5px;
        background: white;
    }
</style>
<!-- selectize plugin -->
<link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/selectize/dist/css/selectize.default.css" />
<?php $this->stop() ?>

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
    ->menu(['text' => __('Properties'), 'tab' => function() use ($form) {
        /** @var \Ffcms\Templex\Template\Template $this */
        $langMenu = $this->bootstrap()->nav('ul', ['class' => 'nav-tabs']);
        $context = $form->fieldset()->text('path', null, __('Slug of URL pathway for this content item'))
            . $form->fieldset()->select('categoryId', ['options' => ContentCategory::getSortedCategories(), 'optionsKey' => true, 'multiple' => null], __('Select content category'));

        foreach (\App::$Properties->get('languages') as $lang) {
            $langMenu->menu([
                'text' => Str::upperCase($lang),
                'tab' => function() use ($form, $lang) {
                    return $form->fieldset()->text('metaTitle.' . $lang, null, __('Set meta title for content page (displayed in browser head). Recommended length: 50-70 chars')).
                        $form->fieldset()->text('metaKeywords.' . $lang, ['class' => 'tag-selectize'], __('Set meta keywords for this content (for search engine crawlers) separated by comma')).
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
    ->menu(['text' => __('Other'), 'tab' => function() use ($form, $model) {
        return $form->fieldset()->boolean('display', null, __('Can users view this content or only available for administrators?')) .
            $form->fieldset()->boolean('important', null, __('Make this material important and stack it on top of all news?')) .
            $form->fieldset()->text('createdAt', ['class' => 'form-control datepick'], __('Set the date of creation or leave empty for current date')) .
            $form->fieldset()->select('authorId', ['options' => $model->getUserIdName(), 'optionsKey' => true, 'class' => 'selectize-option'], __('Enter author user_id or leave empty to set current user as author')) .
            $form->fieldset()->text('source', null, __('Set source URL if this content is copied from another website')) .
            $form->fieldset()->text('addRating', null, __('Add or reduce this content rating. Example: 5 gives +5 to total rating, -5 gives -5 to total'));
    }]);

if (!$model->isNew()) {
    $menu->menu(['text' => __('Comments'), 'tab' => function() use ($form, $model) {
        /** @var \Ffcms\Templex\Template\Template $this */
        $comments = $model->getComments();

        if (!$comments || $comments->count() < 1) {
            return $this->bootstrap()->alert('warning', __('No comments found'));
        }

        $table = $this->table(['class' => 'table table-striped'])
            ->head([
                ['text' => '#'],
                ['text' => __('Message')],
                ['text' => __('Answers')],
                ['text' => __('Author')],
                ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
            ]);

        foreach ($comments as $comment) {
            $table->row([
                ['text' => $comment->id],
                ['text' => Text::snippet(\App::$Security->strip_tags($comment->message), 100)],
                ['text' => $comment->getAnswerCount()],
                ['text' => Url::a(['user/update', [$comment->user_id]], ($comment->user->profile->nick ?? 'id' . $comment->user->id)), 'html' => true],
                ['text' => $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm'])
                    ->add('<i class="fa fa-pencil"></i>', ['comments/read', [$comment->id]], ['html' => true, 'class' => 'btn btn-primary', 'target' => '_blank'])
                    ->add('<i class="fa fa-trash-o"></i>', ['comments/delete', ['comment', $comment->id]], ['html' => true, 'class' => 'btn btn-danger', 'target' => '_blank'])
                    ->display(),'properties' => ['class' => 'text-center'], 'html' => true
                ]
            ]);
        }
        return (new Dom())->div(function() use ($table){
            return $table->display();
        }, ['class' => 'table-responsive']);
    }]);
}

echo $menu->display();
echo $form->button()->submit(__('Save'), ['class' => 'btn btn-primary mt-2']);

?>

<?= $form->field()->hidden('galleryFreeId') ?>

<?= $form->stop() ?>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>
<!-- jquery ui plugin -->
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/jquery-ui-dist/jquery-ui.min.js"></script>
<!-- tinymce plugin -->
<?= \Widgets\Tinymce\Tinymce::widget([
    'config' => 'full'
]); ?>
<!-- dropzone js plugin -->
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/dropzone/dist/dropzone.js"></script>
<script>Dropzone.autoDiscover = false</script>
<!-- jquery datepicker plugin (over jquery-ui) -->
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/jquery-datepicker/jquery-datepicker.js"></script>
<!-- selectize plugin -->
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/selectize/dist/js/standalone/selectize.min.js"></script>
<script>
$(document).ready(function(){
    var isChanged = false;
    var pathChanged = false;
    $('.datepick').datepicker({
        dateFormat: 'dd.mm.yy'
    });

    // prevent send submit if authorization session gone away
    $('form').submit(function () {
        var valid = false;
        $.ajax({
            async: false,
            type: 'GET',
            url: script_url + '/api/user/auth?lang=' + script_lang,
            contentType: 'json',
            success: function(r){
                if (r.status === 1)
                    valid = true;
            }
        });
        if (!valid) {
            alert('<?= __('Attention! Your session is deprecated. You need to make auth in new window!') ?>');
            return false;
        }
        window.onbeforeunload = null;
    });

    // listen form(input,textarea) changes event
    $('input,textarea').keyup(function(){
        isChanged = true;
    });

    // check if changes is saved before page is unloaded
    window.onbeforeunload = function(e){
        if (!isChanged)
            return;

        var msg = 'Page not saved! Please save changes!';
        if (typeof e === 'undefined')
            e = window.event;

        if (e)
            e.returnValue = msg;
        return msg;
    };

    // dropzone gallery file listing and display
    // gallery file listing
    $.getJSON(script_url + "/api/content/gallerylist/<?= $model->galleryFreeId ?>?lang=" + script_lang, function (data) {
        if (data.status !== 1)
            return;
        $.each(data.files, function (index, file) {
            var DropzoneObj = Dropzone.forElement('#ffcms-dropzone');
            var FileObj = {name: file.name, size: file.size, status: Dropzone.ADDED, accepted: true};
            DropzoneObj.emit('addedfile', FileObj);
            DropzoneObj.emit('thumbnail', FileObj, file.thumbnailUrl);
            DropzoneObj.emit('complete', FileObj);
            DropzoneObj.files.push(FileObj);

            var option = '<option value="' + file.name + '">' + file.name + '</option>';
            if (file.name == '<?= $model->poster ?>') {
                option = '<option value="' + file.name + '" selected>' + file.name + '</option>';
            }
            $('#FormContentUpdate-poster').append(option);
        });
    });

    // manual initialize & configure dropzone file uploading
    var DropzoneFiles = [];
    $('#ffcms-dropzone').dropzone({
        url: script_url + '/api/content/galleryupload/<?= $model->galleryFreeId ?>?lang=' + script_lang,
        dictDefaultMessage: '<?= __('Drop files here to upload in gallery') . '<br />' . __('(or click here)') ?>',
        acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp",
        addRemoveLinks: true,
        removedfile: function (file) { // file remove click, lets try to remove file from server & make visual changes
            var serverFile = DropzoneFiles[file.name] != null ? DropzoneFiles[file.name] : file.name;
            $.getJSON(script_url + "/api/content/gallerydelete/<?= $model->galleryFreeId ?>?lang=" + script_lang + "&file=" + serverFile, function (data) {
                if (data.status === 1) {
                    if (file.previewElement != null)
                        return file.previewElement.parentNode.removeChild(file.previewElement);
                }
                return void 0;
            });
        },
        success: function (file, response) { // upload is successful done. Lets try to check server response & build file list
            // save files as array ClientFileName => ServerFileName
            if (response.status !== 1) {
                if (file.previewElement != null)
                    file.previewElement.parentNode.removeChild(file.previewElement);
                alert(response.message);
                return;
            }
            DropzoneFiles[file.name] = response.file.name;
            // add to <select> poster options
            var posterOption = '<option value="' + response.file.name + '">' + file.name + '</option>';
            $('#FormContentUpdate-poster').append(posterOption);
            //console.log('Client file: [' + file.name + ']/Server file:[' + response.file.name + ']');
        }
    });

    // prepare selectize features
    $('.tag-selectize').selectize({
        delimiter: ',',
        persist: false,
        create: function (input) {
            return {
                value: input,
                text: input
            }
        }

    });
    $('.selectize-option').selectize({
        sortField: 'text'
    });

});
</script>
<?php $this->stop() ?>
