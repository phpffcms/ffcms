<?php
use Ffcms\Core\Helper\Url;

/** @var $connector string */

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Files')
];
$this->title = __('Files');
// add library
\App::$Alias->setCustomLibrary('js', 'https://code.jquery.com/jquery-migrate-1.2.1.js');
\App::$Alias->setCustomLibrary('css', \App::$Alias->getVendor('css', 'jquery-ui'));
\App::$Alias->setCustomLibrary('js', \App::$Alias->getVendor('js', 'jquery-ui'));
\App::$Alias->setCustomLibrary('css', \App::$Alias->scriptUrl . '/vendor/phpffcms/ffcms-elfinder/css/elfinder.min.css');
\App::$Alias->setCustomLibrary('js', \App::$Alias->scriptUrl . '/vendor/phpffcms/ffcms-elfinder/js/elfinder.min.js');


?>
<?php \App::$Alias->addPlainCode('js', '$().ready(function() {
        var elf = $("#elfinder").elfinder({
            url : "' . $connector . '",  // connector URL (REQUIRED)
            lang: "' . \App::$Request->getLanguage() . '",             // language (OPTIONAL)
        }).elfinder("instance");
    });'); ?>
<h1><?= __('File management') ?></h1>
<hr />
<div class="row">
    <div class="col-md-12">
        <div id="elfinder"></div>
    </div>
</div>
<!-- sounds like a crazy, just fix elfinder + jquery-ui + bootstrap shits -->
<style>
    .elfinder .elfinder-button {
        -webkit-box-sizing: content-box;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
    }
</style>