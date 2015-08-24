<?php

/** @var $model Apps\Model\Admin\Content\FormContentUpdate */
/** @var $this object */

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Type\String;
use Ffcms\Core\Helper\Url;
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;

$this->title = __('Content update');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/index') => __('Contents'),
    __('Content manage')
];

echo Ffcms\Widgets\Ckeditor\Widget::widget(['targetClass' => 'wysiwyg']);

?>

<?= $this->render('content/_tabs') ?>

<h1><?= __('Content manage') ?></h1>
<hr />
<?php
$form = new Form(
    $model,
    ['action' => ''],
    ['base' => '<div class="form-group"><label for="%name%" class="col-md-3 control-label"><span class="pull-right" style="padding-top: 5px;">%label%</span></label><div class="col-md-9">%item% <p class="help-block">%help%</p></div></div>']
);

$formFullFieldStructure = '<div class="form-group"><label for="%name%">%label%</label>%item% <p class="help-block">%help%</p></div>';

$generalTab = null;
$generalItems = [];
$propertiesItems = [];
// generate language tabs
foreach (\App::$Property->get('languages') as $lang) {
    $generalItems[] = [
        'type' => 'tab',
        'text' => __('Lang') . ': ' . String::upperCase($lang),
        'content' => $form->field('title.' . $lang, 'text', ['class' => 'form-control'], __('Please, enter the title of your material for current language locale'), $formFullFieldStructure) .
            $form->field('text.' . $lang, 'textarea', ['class' => 'form-control wysiwyg', 'rows' => 7, 'html' => true], null, $formFullFieldStructure),
        'html' => true,
        'active' => $lang === \App::$Request->getLanguage(),
        '!secure' => true
    ];

    $propertiesItems[] = [
        'type' => 'tab',
        'text' => __('Lang') . ': ' . String::upperCase($lang),
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

$galleryTab = '<div class="row">
    <div id="gallery-files"></div>
</div>

<div class="row">
<div class="col-md-4">
            <span class="btn btn-success fileinput-button btn-block">
                <i class="glyphicon glyphicon-plus"></i>
                <span>' . __('Upload image') . '</span>
                <!-- The file input field used as target for the file upload widget -->
                <input id="fileupload" type="file" name="gallery-files" multiple>
            </span>
</div>
<div class="col-md-8">
    ' . $form->field('poster', 'select', ['options' => ['1', '2'], 'class' => 'form-control']) . '
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
<?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->finish() ?>

<?php
// load max length display plugin
\App::$Alias->setCustomLibrary('js', \App::$Alias->currentViewUrl . '/assets/js/plugins/maxlength.js');
// load datapicker plugin
\App::$Alias->setCustomLibrary('css', \App::$Alias->currentViewUrl . '/assets/css/plugins/datapick/datapick.css');
\App::$Alias->setCustomLibrary('js', \App::$Alias->currentViewUrl . '/assets/js/plugins/datapick.js');
// load jquery-upload plugin
\App::$Alias->setCustomLibrary('css', \App::$Alias->scriptUrl . '/vendor/bower/blueimp-file-upload/css/jquery.fileupload.css');
\App::$Alias->setCustomLibrary('js', \App::$Alias->scriptUrl . '/vendor/bower/blueimp-file-upload/js/vendor/jquery.ui.widget.js');
\App::$Alias->setCustomLibrary('js', \App::$Alias->scriptUrl . '/vendor/bower/blueimp-file-upload/js/jquery.iframe-transport.js');
\App::$Alias->setCustomLibrary('js', \App::$Alias->scriptUrl . '/vendor/bower/blueimp-file-upload/js/jquery.fileupload.js');
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
                        response = jQuery.parseJSON(response);
                        if (response.status === 1) {
                            is_fail = false;
                        }
                    }
                });
                if(is_fail) {
                    alert('Attention! Your session is deprecated. You need to make auth in new window!');
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

            // pathway from title
            $('#FormContentUpdate-title-<?= \App::$Request->getLanguage() ?>').on('keyup', function() {
                if (pathChanged === true) {
                    return false;
                }
                pathObject.val(translit($(this).val()));
            });

            // gallery file listing
            $.getJSON(script_url+"/api/content/gallerylist/<?= $model->galleryFreeId ?>?lang="+script_lang,
                function (data) {
                    $.each(data.files, function (index, file) {
                        $('<div/>').html(
                            '<div class="col-md-2 well" style="margin-left: 5px;">'+
                            '<div class="text-center"><strong>'+file.name+'</strong></div>'+
                            '<img src="'+script_url+file.thumbnailUrl+'" class="img-responsive image-item" />'+
                            '<div class="text-center">' +
                            '<a href="#" class="label label-info">View</a> '+
                            '<a href="#" class="label label-danger">Delete</a>'+
                            '</div></div>'
                        ).appendTo('#gallery-files');
                    });
                });

            // gallery file upload
            $('#fileupload').fileupload({
                url: script_url+'/api/content/galleryupload/<?= $model->galleryFreeId ?>?lang='+script_lang,
                dataType: 'json',
                done: function (e, data) {
                    $.each(data.result.files, function (index, file) {
                        $('<div/>').html(
                            '<div class="col-md-2 well" style="margin-left: 5px;">'+
                            '<div class="text-center"><strong>'+file.name+'</strong></div>'+
                            '<img src="'+script_url+file.thumbnailUrl+'" class="img-responsive image-item" />'+
                            '</div>'
                        ).appendTo('#gallery-files');
                    });
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');

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
            }
        });
    });
</script>