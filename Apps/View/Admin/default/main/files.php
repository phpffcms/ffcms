<?php
use Ffcms\Core\Helper\Url;

/** @var $connector string */

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Files')
];
$this->title = __('Files');
// add library
\App::$Alias->setCustomLibrary('css', \App::$Alias->getVendor('css', 'jquery-ui'));
\App::$Alias->setCustomLibrary('js', \App::$Alias->getVendor('js', 'jquery-ui'));
\App::$Alias->setCustomLibrary('css', \App::$Alias->scriptUrl . '/vendor/studio-42/elfinder/css/elfinder.min.css');
\App::$Alias->setCustomLibrary('js', \App::$Alias->scriptUrl . '/vendor/studio-42/elfinder/js/elfinder.min.js');
if (\App::$Request->getLanguage() !== 'en') {
    \App::$Alias->setCustomLibrary('js', \App::$Alias->scriptUrl . '/vendor/studio-42/elfinder/js/i18n/elfinder.' . \App::$Request->getLanguage() . '.js');
}
?>

<h1><?= __('File management') ?></h1>
<hr />
<div class="row">
    <div class="col-md-12">
        <div id="elfinder"></div>
    </div>
</div>
<script>
    $(document).ready(function(){
        var elf = $('#elfinder').elfinder({
            url: '<?=$connector?>',
            lang: '<?= \App::$Request->getLanguage() ?>'
        }).elfinder('instance');
    })
</script>