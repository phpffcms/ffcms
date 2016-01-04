<?php

/** @var $model Apps\Model\Admin\Content\FormContentUpdate */
/** @var $this object */

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;

$this->title = __('Content update');

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
    ['base' => '<div class="form-group"><label for="%name%" class="col-md-3 control-label"><span class="pull-right" style="padding-top: 5px;">%label%</span></label><div class="col-md-9">%item% <p class="help-block">%help%</p></div></div>']
);

$formFullFieldStructure = '<div class="form-group"><label for="%name%">%label%</label>%item% <p class="help-block">%help%</p></div>';

$generalTab = null;
$generalItems = [];
$propertiesItems = [];
// generate language tabs
foreach (\App::$Properties->get('languages') as $lang) {
    $generalItems[] = [
        'type' => 'tab',
        'text' => __('Lang') . ': ' . Str::upperCase($lang),
        'content' => $form->field('title.' . $lang, 'text', ['class' => 'form-control'], __('Please, enter the title of your material for current language locale'), $formFullFieldStructure) .
            $form->field('text.' . $lang, 'textarea', ['class' => 'form-control wysiwyg', 'rows' => 7, 'html' => true], null, $formFullFieldStructure),
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
<div class="col-md-4">
            <span class="btn btn-success fileinput-button btn-block">
                <i class="glyphicon glyphicon-plus"></i>
                <span>' . __('Upload image') . '</span>
                <!-- The file input field used as target for the file upload widget -->
                <input id="fileupload" type="file" name="gallery-files" multiple>
            </span>
</div>
<div class="col-md-8">
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

<!-- dom model for gallery items -->
<div class="col-md-3 well hidden" id="gallery-item">
    <div class="text-center"><strong id="item-title"></strong></div>
    <img id="item-image" src="" class="img-responsive image-item"/>
    <div class="text-center">
        <a id="item-view-link" href="#" target="_blank" class="label label-info"><?= __('View') ?></a>
        <a id="item-delete-link" href="javascript:void(0);" class="label label-danger delete-gallery-item"><?= __('Delete') ?></a>
    </div>
</div>

<script>
    window.jQ.push(function(){
        $(function(){
            // onbeforeUnload hook
            var isChanged = false;
            var pathChanged = false;
            var galleryItem = $('#gallery-item').clone().removeClass('hidden').removeAttr('id');
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

            // pathway from title
            $('#FormContentUpdate-title-<?= \App::$Request->getLanguage() ?>').on('keyup', function() {
                if (pathChanged === true) {
                    return false;
                }
                pathObject.val(translit($(this).val()));
            });

            // gallery remove
            $(document).on('click', '.delete-gallery-item', function() {
                var itemId = (this.id);
                $.getJSON(script_url+"/api/content/gallerydelete/<?= $model->galleryFreeId ?>/"+(this.id)+"?lang="+script_lang, function (data){
                    if (data.status === 1) {
                        document.getElementById('image-'+itemId).remove();
                    } else {
                        alert('Could not delete this image: ' + itemId);
                    }
                });
            });

            // gallery file listing
            $.getJSON(script_url+"/api/content/gallerylist/<?= $model->galleryFreeId ?>?lang="+script_lang,
                function (data) {
                    $.each(data.files, function (index, file) {
                        var gItem = galleryItem.clone();
                        // make dom for gallery item
                        gItem.attr('id', 'image-'+file.name);
                        gItem.find('#item-title').text(file.name).removeAttr('id');
                        gItem.find('#item-image').attr('src', script_url+file.thumbnailUrl).removeAttr('id');
                        gItem.find('#item-view-link').attr('href', script_url + file.url).removeAttr('id');
                        gItem.find('#item-delete-link').attr('id', file.name);
                        $('#gallery-files').append(gItem);

                        var option = '<option value="'+file.name+'">'+file.name+'</option>';
                        if (file.name == '<?= $model->poster ?>') {
                            option = '<option value="'+file.name+'" selected>'+file.name+'</option>';
                        }
                        $('#FormContentUpdate-poster').append(option);
                    });
                });

            // gallery file upload
            $('#fileupload').fileupload({
                url: script_url+'/api/content/galleryupload/<?= $model->galleryFreeId ?>?lang='+script_lang,
                dataType: 'json',
                done: function (e, data) {
                    if (data.result.status !== 1) {
                        alert(data.result.message);
                    }
                    $.each(data.result.files, function (index, file) {
                        var gItem = galleryItem.clone();
                        // make dom for gallery item
                        gItem.attr('id', 'image-'+file.name);
                        gItem.find('#item-title').text(file.name).removeAttr('id');
                        gItem.find('#item-image').attr('src', script_url+file.thumbnailUrl).removeAttr('id');
                        gItem.find('#item-view-link').attr('href', script_url + file.url).removeAttr('id');
                        gItem.find('#item-delete-link').attr('id', file.name);
                        $('#gallery-files').append(gItem);

                        var option = '<option value="'+file.name+'">'+file.name+'</option>';
                        if (file.name == '<?= $model->poster ?>') {
                            option = '<option value="'+file.name+'" selected>'+file.name+'</option>';
                        }
                        $('#FormContentUpdate-poster').append(option);
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