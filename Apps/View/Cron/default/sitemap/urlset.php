<?php
/** @var array $items */
?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($items as $item): ?>
        <url>
            <loc><?= $item['uri'] ?></loc>
            <lastmod><?= $item['lastmod'] ?></lastmod>
            <changefreq><?= $item['freq'] ?></changefreq>
            <priority><?= $item['priority'] ?></priority>
        </url>
    <?php endforeach; ?>
</urlset>