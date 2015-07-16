<?php

/** @var $model Apps\Model\Front\Content\EntityContentRead */
/** @var $this object */
/** @var $trash bool */
use Ffcms\Core\Helper\Arr;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Helper\Url;

$this->title = \App::$Security->strip_tags($model->title);

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
    <h1><?= $this->title ?></h1>
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

</article>