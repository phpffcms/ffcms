<?php
    /** @var $model \Apps\Model\Front\Sitemap\EntityIndexList */
use Ffcms\Core\Helper\Type\Obj;

?>
<?= '<?xml version="1.0" encoding="UTF-8"?>'?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($model->getInfo() as $info): ?>
    <sitemap>
        <loc><?= $info['loc'] ?></loc>
        <lastmod><?= $info['lastmod'] ?></lastmod>
    </sitemap>
<?php endforeach; ?>
</sitemapindex>