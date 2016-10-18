<?php

/** @var $model Apps\Model\Admin\Content\FormContentUpdate */
/** @var $this object */

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

$this->title = __('Content edit');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/index') => __('Contents'),
    __('Content manage')
];

echo Ffcms\Widgets\Ckeditor\Ckeditor::widget(['targetClass' => 'wysiwyg', 'config' => 'config-full']);

?>

<?= $this->render('content/_tabs') ?>

<h1><?= __('Content manage') ?></h1>
<hr />
<?php
$form = new Form(
    $model,
    ['action' => ''],
    ['base' => 'content/form/base_content_update']
);

echo $form->start();

$generalTab = null;
$generalItems = [];
$propertiesItems = [];
// generate language tabs
foreach (\App::$Properties->get('languages') as $lang) {
    $generalItems[] = [
        'type' => 'tab',
        'text' => __('Lang') . ': ' . Str::upperCase($lang),
        'content' => $form->field('title.' . $lang, 'text', ['class' => 'form-control'], __('Please, enter the title of your material for current language locale'), 'content/form/tab_content_update') .
            $form->field('text.' . $lang, 'textarea', ['class' => 'form-control wysiwyg', 'rows' => 7, 'html' => true], null, 'content/form/tab_content_update'),
        'html' => true,
        'active' => $lang === \App::$Request->getLanguage(),
        '!secure' => true
    ];

    $propertiesItems[] = [
        'type' => 'tab',
        'text' => __('Lang') . ': ' . Str::upperCase($lang),
        'content' => $form->field('metaTitle.' . $lang, 'text', ['class' => 'form-control'], __('Enter meta param title for page title. Recoomended: 50-70 characters')) .
            $form->field('metaKeywords.' . $lang, 'text', ['class' => 'form-control'], __('Enter meta param keywords for this content, separated by comma. Example: home, door, dog')) .
            $form->field('metaDescription.' . $lang, 'text', ['class' => 'form-control'], __('Enter meta param description for this content. Recommended is 100-150 characters')),
        'html' => true,
        '!secure' => true,
        'active' => $lang === \App::$Request->getLanguage()
    ];
}

$generalTab = Nav::display([
    'property' => ['class' => 'nav-pills'],
    'blockProperty' => ['class' => 'nav-locale-block'],
    'tabAnchor' => 'content-update-general-locale',
    'items' => $generalItems
]);

$propertiesTab = $form->field('path', 'text', ['class' => 'form-control'], __('Slug of URL pathway for this content item'));
$propertiesTab .= $form->field('categoryId', 'select', ['class' => 'form-control', 'size' => 4, 'options' => ContentCategory::getSortedCategories(), 'optionsKey' => true], __('Select content category'));
$propertiesTab .= Nav::display([
    'property' => ['class' => 'nav-pills'],
    'tabAnchor' => 'content-update-properties-locale',
    'items' => $propertiesItems
]);

$otherTab = $form->field('display', 'checkbox', null, __('Can users view this content or it only available for administrators?'));
$otherTab .= $form->field('createdAt', 'text', ['class' => 'form-control datapick'], __('Set the date of creation or less this field empty to set current'));
$otherTab .= $form->field('authorId', 'text', ['class' => 'form-control'], __('Enter user id to change author of this content'));
$otherTab .= $form->field('source', 'text', ['class' => 'form-control'], __('Set the source URL if this content is copied from someone other url'));
$otherTab .= $form->field('addRating', 'text', ['class' => 'form-control'], __('Add or reduce rating of this content. Example: 5 gives +5 to total rating, -5 gives -5 to total'));

$galleryTab = '<div class="row" id="gallery-files"></div>

<div class="row">
<div class="col-md-8">
    <div class="dropzone dropzone-previews" id="ffcms-dropzone"></div>
</div>
<div class="col-md-4">
    ' . $form->field('poster', 'select', ['options' => [__('Not selected...')], 'class' => 'form-control'], __('Select image from gallery as a poster for this content')) . '
</div>
</div><br/><br/>';
?>
<?= Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'tabAnchor' => 'content-update',
    'items' => [
        ['type' => 'tab', 'text' => __('General'), 'content' => $generalTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Properties'), 'content' => $propertiesTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Gallery'), 'content' => $galleryTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Other'), 'content' => $otherTab, 'html' => true, '!secure' => true]
    ]
]);?>

<?= $form->field('galleryFreeId', 'hidden') ?>
<?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>&nbsp;
<?= Url::link(['content/index'], __('Cancel'), ['class' => 'btn btn-default']) ?>
<?= $form->finish() ?>

<?php
// load max length display plugin
\App::$Alias->setCustomLibrary('js', \App::$Alias->currentViewUrl . '/assets/js/plugins/maxlength.js');
// load datapicker plugin
\App::$Alias->setCustomLibrary('css', \App::$Alias->currentViewUrl . '/assets/css/plugins/datapick/datapick.css');
\App::$Alias->setCustomLibrary('js', \App::$Alias->currentViewUrl . '/assets/js/plugins/datapick.js');
// load jquery-upload plugin
\App::$Alias->setCustomLibrary('css', \App::$Alias->scriptUrl . '/vendor/bower/dropzone/dist/min/dropzone.min.css');
\App::$Alias->setCustomLibrary('css', \App::$Alias->scriptUrl . '/vendor/bower/dropzone/dist/min/basic.min.css');
\App::$Alias->setCustomLibrary('js', \App::$Alias->scriptUrl . '/vendor/bower/dropzone/dist/min/dropzone.min.js');
?>

<script>
    window.jQ.push(function(){
        $(function(){
            // onbeforeUnload hook
            var isChanged = false;
            var pathChanged = false;
            // init ckeditor
            CKEDITOR.disableAutoInline = true;
            // init maxlength plugin
            $('input[maxlength]').maxlength();
            // init datapick plugin
            $('.datapick').datepicker({
                format: 'dd.mm.yyyy'
            });

            // prevent sending form if session is closed
            $('form').submit(function() {
                var is_fail = true;
                $.ajax({
                    async: false,
                    type: 'GET',
                    url: script_url + '/api/user/auth?lang='+script_lang,
                    contentType: 'json',
                    success: function(response) {
                        if (response.status === 1) {
                            is_fail = false;
                        }
                    }
                });
                if(is_fail) {
                    alert('<?= __('Attention! Your session is deprecated. You need to make auth in new window!') ?>');
                    return false;
                }
                window.onbeforeunload = null;
            });
            // if something in form is changed - lets set isChanged
            $('input,textarea').keyup(function(){
                isChanged = true;
            });

            var pathObject = $('#FormContentUpdate-path');
            if (pathObject.val().length > 1) {
                pathChanged = true;
            }
            pathObject.on('keyup', function(){
				pathChanged = true;
            });


            // pathway from title
            $('#FormContentUpdate-title-<?= \App::$Request->getLanguage() ?>').on('keyup', function() {
                if (pathChanged === true) {
                    return false;
                }
                pathObject.val(translit($(this).val()));
            });

            window.onbeforeunload = function (evt) {
                if (!isChanged) return;
                var message = "Page is not saved!";
                if (typeof evt == "undefined") {
                    evt = window.event;
                }
                if (evt) {
                    evt.returnValue = message;
                }
                return message;
            };

            // gallery file listing
            $.getJSON(script_url+"/api/content/gallerylist/<?= $model->galleryFreeId ?>?lang="+script_lang, function (data) {
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

            // initialize & configure dropzone file uploading
            Dropzone.autoDiscover = false;
            var DropzoneFiles = [];
            $('#ffcms-dropzone').dropzone({
                url: script_url+'/api/content/galleryupload/<?= $model->galleryFreeId ?>?lang='+script_lang,
                dictDefaultMessage: '<?= __('Drop files here to upload in gallery') . '<br />' . __('(or click here)') ?>',
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp",
                addRemoveLinks: true,
                removedfile: function (file) { // file remove click, lets try to remove file from server & make visual changes
                    var serverFile = DropzoneFiles[file.name] != null ? DropzoneFiles[file.name] : file.name;
                    $.getJSON(script_url+"/api/content/gallerydelete/<?= $model->galleryFreeId ?>?lang="+script_lang+"&file="+serverFile, function(data){
                        if (data.status === 1) {
                            if (file.previewElement != null)
                                return file.previewElement.parentNode.removeChild(file.previewElement);
                        }
                        return void 0;
                    });
                },
                success: function(file, response) { // upload is successful done. Lets try to check server response & build file list
                    // save files as array ClientFileName => ServerFileName
                    if (response.status !== 1) {
                        if (file.previewElement != null)
                            file.previewElement.parentNode.removeChild(file.previewElement);
                        alert(response.message);
                        return;
                    }
                    DropzoneFiles[file.name] = response.file.name;
                    // add to <select> poster options
                    var posterOption = '<option value="'+response.file.name+'">'+file.name+'</option>';
                    $('#FormContentUpdate-poster').append(posterOption);

                    console.log('Client file: ['+file.name +']/Server file:[' + response.file.name+']');
                }
            });
        });
    });
</script>