<?php

/** @var $model Apps\Model\Front\Content\EntityContentRead */
/** @var $this object */
/** @var $trash bool */
/** @var $configs array */
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Object;
use Ffcms\Core\Helper\Type\String;
use Ffcms\Core\Helper\Url;

// set meta title
$this->title = \App::$Security->strip_tags($model->metaTitle);
if (String::likeEmpty($this->title)) {
    $this->title = \App::$Security->strip_tags($model->title);
}
// set meta description
$this->description = \App::$Security->strip_tags($model->description);
// set meta keywords
if (Object::isArray($model->keywords) && count($model->keywords) > 0) {
    $this->keywords = implode(', ', $model->keywords);
}

$breadcrumbs = [
    Url::to('/') => __('Home')
];
if (Object::isArray($model->catNesting)) {
    foreach ($model->catNesting as $cat) {
        if ($cat['path'] === '') {
            $breadcrumbs[Url::to('content/list', $cat['path'])] = __('Contents');
        } else {
            $breadcrumbs[Url::to('content/list', $cat['path'], null, [], false)] = $cat['name'];
        }
    }
    $breadcrumbs[] = __('Content') . ': ' . String::substr($this->title, 0, 40);
}

$this->breadcrumbs = $breadcrumbs;
?>

<?php
$properties = [
    'date' => $model->getCategory()->getProperty('showDate') === '1',
    'author' => $model->getCategory()->getProperty('showAuthor') === '1',
    'views' => $model->getCategory()->getProperty('showViews') === '1',
    'category' => $model->getCategory()->getProperty('showCategory') === '1'
];
$showComments = $model->getCategory()->getProperty('showComments') === '1';
$showPoster = $model->getCategory()->getProperty('showPoster') === '1';
?>

<article class="article-item">
    <h1><?= \App::$Security->strip_tags($model->title); ?></h1>
    <?php if (Arr::in(true, $properties)): ?>
    <div class="meta">
        <?php if ($properties['category'] === true): ?>
        <span><i class="fa fa-list"></i><?= Url::link(['content/list', $model->catPath, null, [], false], $model->catName, ['itemprop' => 'genre']) ?></span>
        <?php endif; ?>
        <?php if ($properties['date'] === true): ?>
        <span><i class="fa fa-calendar"></i><time datetime="<?= date('c', $model->createDate) ?> itemprop="datePublished"><?= $model->createDate ?></time></span>
        <?php endif; ?>
        <?php if ($properties['author'] === true && $model->authorId !== null): ?>
        <span><i class="fa fa-user"></i><?= Url::link(['profile/show', $model->authorId], $model->authorName, ['itemprop' => 'author']) ?></span>
        <?php endif; ?>
        <?php if ($properties['views'] === true): ?>
        <span><i class="fa fa-eye"></i><?= $model->views ?></span>
        <?php endif ?>
    </div>
    <?php else: ?>
    <hr />
    <?php endif; ?>
    <?php if ($trash): ?>
    <p class="alert alert-danger"><i class="fa fa-trash-o"></i> <?= __('This content is placed in trash') ?></p>
    <?php endif; ?>
    <div id="content-text"><?= $model->text ?></div>
    <?php if ((int)$configs['keywordsAsTags'] === 1): ?>
    <div id="content-tags">
        <i class="fa fa-tags"></i>
        <?php
        if (Object::isArray($model->keywords) && count($model->keywords) > 0) {
            foreach ($model->keywords as $tag) {
                $tag = \App::$Security->strip_tags(trim($tag));
                echo Url::link(['content/tag', $tag], $tag, ['class' => 'label label-default']) . "&nbsp;";
            }
        }
        ?>
    </div>
    <?php endif; ?>
    <?php if (!String::likeEmpty($model->source)): ?>
    <div id="content-source" style="padding-top: 5px;">
        <?php
        $sourceUrl = \App::$Security->strip_tags($model->source);
        $parseUrl = parse_url($sourceUrl);
        $sourceHost = $parseUrl['host'];
        ?>
        <i class="fa fa-thumb-tack"></i> <?= __('Source') ?>: <a href="<?= $sourceUrl ?>" rel="nofollow" target="_blank">
            <?= $sourceHost ?>
        </a>
    </div>
    <?php endif; ?>
</article>