<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Front\Sitemap\EntityIndexList $model */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Sitemap'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        __('Sitemap')
    ]
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Sitemap') ?></h1>

<p><?= __('Sitemap its a special application to generate sitemap as xml file over sitemap standart for search engines.') ?></p>
<p><?= __('Sitemap main index') ?>: <a href="<?= \App::$Alias->scriptUrl . '/sitemap' ?>" target="_blank">/sitemap</a></p>
<h3><?= __('Sitemap files') ?></h3>
<?php
$items = [];
if (!$model->files || count($model->files) < 1) {
    echo $this->bootstrap()->alert('warning', __('No sitemap files found! Maybe cron manager is not configured'));
    $this->stop();
    return;
}

$list = $this->listing('ul');

foreach($model->files as $file) {
    $list->li(['type' => 'link', 'link' => \Ffcms\Core\App::$Alias->scriptUrl . $file, 'text' => $file, 'linkProperties' => ['target' => '_blank']]);
}
echo $list->display();
?>

<p><?= __('Attention! To generate newest sitemaps you should configure cron manager!') ?></p>

<?php $this->stop() ?>