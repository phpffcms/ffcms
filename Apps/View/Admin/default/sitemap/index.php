<?php
/** @var $this \Ffcms\Core\Arch\View */
/** @var $model \Apps\Model\Front\Sitemap\EntityIndexList */

use Ffcms\Core\Helper\Url;

$this->title = __('Sitemap');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    __('Sitemap')
];
?>
<h1><?= __('Sitemap') ?></h1>
<hr />
<p><?= __('Sitemap its a special application to generate sitemap as xml file over sitemap standart for search engines.') ?></p>
<p><?= __('Sitemap main index') ?>: <a href="<?= \App::$Alias->scriptUrl . '/sitemap' ?>" target="_blank">/sitemap</a></p>
<h3><?= __('Sitemap files') ?></h3>
<?php
$items = [];
if ($model->files === null || count($model->files) < 1) {
    echo '<p class="alert alert-warning">' . __('No sitemap files found! Maybe cron manager is not configured') . '</p>';
    return;
}

foreach($model->files as $file) {
    $items[] = [
        'type' => 'link',
        'link' => \App::$Alias->scriptUrl . $file,
        'text' => $file,
        'linkProperty' => ['target' => '_blank']
    ];
}
echo \Ffcms\Core\Helper\HTML\Listing::display([
    'type' => 'ul',
    'items' => $items
]);
?>
<p><?= __('Attention! To generate newest sitemaps you should configure cron manager!') ?></p>