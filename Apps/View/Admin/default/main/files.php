<?php

use Ffcms\Templex\Url\Url;

/** @var $connector string */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Main\FormSettings $model */

$this->layout('_layouts/default', [
    'title' => __('Files'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        __('Files')
    ]
]);
?>
<?php $this->push('css') ?>
<link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/components/jqueryui/themes/base/jquery-ui.min.css" />
<link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/studio-42/elfinder/css/elfinder.min.css" />
<?php $this->stop() ?>

<?php $this->start('body') ?>
<h1><?= __('File management') ?></h1>
<div class="row">
    <div class="col-md-12">
        <div id="elfinder"></div>
    </div>
</div>
<?php $this->stop() ?>

<?php $this->push('javascript'); ?>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/components/jqueryui/jquery-ui.min.js"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/studio-42/elfinder/js/elfinder.min.js"></script>
<?php if (\App::$Request->getLanguage() !== 'en'): ?>
    <script src="<?= \App::$Alias->scriptUrl ?>/vendor/studio-42/elfinder/js/i18n/elfinder.<?= \App::$Request->getLanguage() ?>.js"></script>
<?php endif; ?>
<script>
    $(document).ready(function(){
        var elf = $('#elfinder').elfinder({
            url: '<?=$connector?>',
            lang: '<?= \App::$Request->getLanguage() ?>'
        }).elfinder('instance');
    })
</script>
<?php $this->stop(); ?>