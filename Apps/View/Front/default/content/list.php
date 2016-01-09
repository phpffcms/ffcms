<?php

use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use \Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Url;

/** @var $pagination object */
/** @var $configs array */
/** @var $this object */
/** @var $category array */
/** @var $model Apps\Model\Front\Content\EntityCategoryRead */

$catConfigs = [];
foreach ($model->categoryData['configs'] as $key=>$value) {
    $catConfigs[$key] = (int)$value === 1;
}

$catMeta = [
    'date' => $catConfigs['showDate'],
    'author' => $catConfigs['showAuthor'],
    'views' => $catConfigs['showViews']
];

$this->title = $model->categoryData['title'];
if (!\App::$Request->isPathInjected()) {
    $this->breadcrumbs = [
            Url::to('/') => __('Home'),
            Url::to('content/list') => __('Contents'),
            $model->categoryData['title']
    ];
}

?>
<?php if (!\App::$Request->isPathInjected()): ?>
    <h1><?= $model->categoryData['title'] ?></h1>
    <?php if (Str::length($model->categoryData['description']) > 0): ?>
    <p><?= $model->categoryData['description'] ?></p>
    <?php endif; ?>
    <hr />
<?php endif; ?>
<?php if (count($model->items) < 1): ?>
    <p class="alert alert-warning"><?= __('This category is empty!') ?></p>
<?php return; endif; ?>
<?php foreach ($model->items as $item): ?>
    <article class="article-item" itemscope="itemscope" itemtype="https://schema.org/NewsArticle">
        <h2 itemprop="name">
            <a href="<?= \App::$Alias->baseUrl . $item['uri'] ?>">
                <?= $item['title'] ?>
            </a>
        </h2>
        <?php if (Arr::in(true, $catMeta)): ?>
        <div class="meta">
            <?php if ($catConfigs['showCategory'] === true): ?>
            <span><i class="fa fa-list"></i>
                <?= Url::link(
                    ['content/list', $item['category']['path']],
                    Serialize::getDecodeLocale($item['category']['title']),
                    ['itemprop' => 'genre']
                ) ?>
            </span>
            <?php endif; ?>
            <?php if ($catConfigs['showDate'] === true): ?>
            <span><i class="fa fa-calendar"></i>
                <time datetime="<?= date('c', Date::convertToTimestamp($item['date'])) ?>" itemprop="datePublished">
                    <?= $item['date'] ?>
                </time>
            </span>
            <?php endif; ?>
            <?php if ($catConfigs['showAuthor'] === true): ?>
            <span><i class="fa fa-user"></i>
                <?php
                $ownerNick = $item['author']->getProfile()->nick;
                if (Str::likeEmpty($ownerNick)) {
                    $ownerNick = __('Unknown');
                }
                if ($item['author']->getId() < 1) {
                    echo '<s>' . $ownerNick . '</s>';
                } else {
                    echo Url::link(['profile/show', $item['author']->getId()], $ownerNick, ['itemprop' => 'author']);
                }
                ?>
            </span>
            <?php endif; ?>
            <?php if ($catConfigs['showViews'] === true): ?>
            <span><i class="fa fa-eye"></i> <?= $item['views'] ?></span>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <hr />
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <?php if ($catConfigs['showPoster'] === true && $item['thumb'] !== null): ?>
                <img src="<?= \App::$Alias->scriptUrl . $item['thumb'] ?>" class="image_poster img-thumbnail" alt="<?= __('Poster for') ?>: <?= Str::lowerCase($item['title']) ?>" />
                <?php endif; ?>
                <div itemprop="text articleBody">
                    <?= $item['text'] ?>
                </div>
            </div>
        </div>
        <div class="meta">
            <?php if ((int)$configs['keywordsAsTags'] === 1 && $item['tags'] !== null && Obj::isArray($item['tags'])): ?>
            <span><i class="fa fa-tags hidden-xs"></i>
                <?php
                    foreach ($item['tags'] as $tag) {
                        $tag = \App::$Security->strip_tags(trim($tag));
                        echo Url::link(['content/tag', $tag], $tag, ['class' => 'label label-default']) . "&nbsp;";
                    }
                ?>
            </span>
            <meta itemprop="keywords" content="<?php implode(',', $item['tags']); ?>">
            <?php endif; ?>
            <span><i class="fa fa-comments"></i>
                <a href="#"><?= __('Comments') ?>: <span itemprop="commentCount">0</span></a>
            </span>
            <span class="pull-right hidden-xs">
                <i class="fa fa-share"></i>
                <a href="<?= \App::$Alias->baseUrl . $item['uri'] ?>">
                    <?= __('Read more') ?>
                </a>
            </span>
        </div>
    </article>

<?php endforeach; ?>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>
