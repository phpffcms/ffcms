<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Front\Content\EntityContentRead $model */
/** @var Apps\Model\Front\Content\EntityContentSearch $search */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var bool $trash */
/** @var array $configs */

// check if content is trashed or hidden from display and show it only for admin with content.index permissions
if (($trash || !$model->display) && (!\App::$User->isAuth() || !\App::$User->identity()->role->can('Admin/Content/Index'))) {
    throw new \Ffcms\Core\Exception\NotFoundException(__('Page not found'));
}
// set meta title
$title = $model->metaTitle;
if (Any::isEmpty($title)) {
    $title = $model->title;
}
// set meta description
$description = $model->metaDescription;
// set meta keywords
$keywords = null;
if (Any::isArray($model->metaKeywords) && count($model->metaKeywords) > 0) {
    $keywords = implode(', ', $model->metaKeywords);
}
// don't use breadcrumbs on injected pathway rule
$breadcrumbs = null;
if (!\App::$Request->isPathInjected() && (bool)$model->getCategory()->getProperty('showCategory')) {
    $breadcrumbs = [
        Url::to('/') => __('Home')
    ];
    if (Any::isArray($model->catNesting)) {
        foreach ($model->catNesting as $cat) {
            if (Str::likeEmpty($cat['path'])) {
                $breadcrumbs[Url::to('content/list', [$cat['path']])] = __('Contents');
            } else {
                $breadcrumbs[Url::to('content/list', [$cat['path']])] = $cat['name'];
            }
        }
        $breadcrumbs[] = __('Content') . ': ' . Str::sub($title, 0, 40);
    }
}

// render with global layout
$this->layout('_layouts/default', [
    'title' => $title,
    'keywords' => $keywords,
    'description' => $description,
    'breadcrumbs' => $breadcrumbs
]);

$properties = [
    'date' => (bool)$model->getCategory()->getProperty('showDate'),
    'author' => (bool)$model->getCategory()->getProperty('showAuthor'),
    'views' => (bool)$model->getCategory()->getProperty('showViews'),
    'category' => (bool)$model->getCategory()->getProperty('showCategory'),
    'rating' => (bool)$model->getCategory()->getProperty('showRating'),
    'tags' => (bool)$model->getCategory()->getProperty('showTags')
];
$showComments = (bool)$model->getCategory()->getProperty('showComments');
$showPoster = (bool)$model->getCategory()->getProperty('showPoster');
?>

<?php $this->push('css') ?>
<link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css" />
<?php $this->stop() ?>

<?php $this->start('body') ?>
    <article class="article-item article-border" itemscope="itemscope" itemtype="https://schema.org/NewsArticle">
        <h1><?= $model->title ?></h1>
        <?php if (Arr::in(true, $properties)): ?>
            <div class="meta">
                <?php if ((bool)$properties['category']): ?>
                    <span class="spaced"><i class="fas fa-list"></i> <?= Url::a(['content/list', [$model->catPath]], $model->catName, ['itemprop' => 'genre']) ?></span>
                <?php endif; ?>
                <?php if ((bool)$properties['date']): ?>
                    <span class="spaced"><i class="far fa-calendar"></i> <time datetime="<?= date('c', Date::convertToTimestamp($model->getRecord()->created_at)) ?> itemprop="datePublished"><?= $model->createDate ?></time></span>
                <?php endif; ?>
                <?php if ((bool)$properties['author']): ?>
                    <?php if ($model->authorId && $model->authorId > 0): ?>
                        <span class="spaced"><i class="fas fa-user"></i> <?= Url::a(['profile/show', [$model->authorId]], $model->authorName, ['itemprop' => 'author']) ?></span>
                    <?php else: ?>
                        <span class="spaced"><i class="fas fa-user"></i> <s><?= $model->authorName ?></s></span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ((bool)$properties['views']): ?>
                    <span class="spaced"><i class="fas fa-eye"></i> <?= $model->views ?></span>
                <?php endif ?>
                <?php if (\App::$User->isAuth() && \App::$User->identity()->role->can('Admin/Content/Update')): ?>
                    <span class="float-end"><a href="<?= \App::$Alias->scriptUrl . '/admin/content/update/' . $model->id ?>" target="_blank"><i class="fas fa-pencil-alt" style="color: #ff0000;"></i></a></span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <hr />
        <?php endif; ?>
        <?php if ($trash): ?>
            <p class="alert alert-danger"><i class="fas fa-trash-alt"></i> <?= __('This content is placed in trash') ?></p>
        <?php endif; ?>
        <?php if (!$model->display): ?>
            <p class="alert alert-warning"><i class="fas fa-pencil-alt"></i> <?= __('This content now is on moderation stage') ?></p>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <div id="content-text">
                    <?php if ($showPoster && $model->posterFull && $model->posterThumb): ?>
                        <a href="<?= $model->posterFull ?>" data-fancybox="image">
                            <img alt="<?= __('Poster for') ?>: <?= Str::lowerCase($model->title) ?>" src="<?= \App::$Alias->scriptUrl . $model->posterThumb ?>" class="image_poster img-thumbnail" />
                        </a>
                    <?php endif ;?>
                    <?= $model->text ?>
                </div>
            </div>
        </div>
        <?php if ($model->galleryItems && Any::isArray($model->galleryItems)): ?>
            <div class="row mb-4">
                <?php foreach ($model->galleryItems as $thumb => $full): ?>
                <div class="col-6 col-lg-4 mt-2">
                    <figure>
                        <a class="d-block mb-4" data-fancybox="images" href="<?= \App::$Alias->scriptUrl . $full ?>">
                            <img class="img-fluid" src="<?= \App::$Alias->scriptUrl . $thumb ?>" alt="gallery image">
                        </a>
                    </figure>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($search->items && Any::isArray($search->items)): ?>
            <div class="h3"><?= __('Similar content') ?></div>
            <div class="accordion" id="accordion-similar-group">
            <?php $idx = 1; ?>
            
            <?php foreach ($search->items as $item): ?>
                <div class="card">
                    <div class="card-header" id="similar-heading-<?= $item['id'] ?>">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#similar-collapse-<?= $item['id'] ?>" aria-expanded="false" aria-controls="similar-collapse-<?= $item['id'] ?>">
                                <?= $item['title'] ?>
                            </button>
                        </h5>
                    </div>
                    <div id="similar-collapse-<?= $item['id'] ?>" class="accordion-collapse collapse" aria-labelledby="similar-heading-<?= $item['id'] ?>" data-parent="#accordion-similar-group">
                        <div class="card-body">
                            <a href="<?= \App::$Alias->baseUrl . $item['uri'] ?>">
                                <?= $item['snippet'] ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php ++$idx; ?>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    <?php if ($properties['rating']) {
                        /**echo \App::$View->render('content/_rate', [
                            'id' => $model->id,
                            'rating' => $model->rating,
                            'canRate' => $model->canRate
                        ]);*/
                    } ?>
                    <?php //\App::$View->render('content/_rateJs') ?>
                </div>
                <div class="float-end">
                    <?php if ($properties['tags']): ?>
                        <div id="content-tags">
                            <?php
                            if (Any::isArray($model->metaKeywords) && count($model->metaKeywords) > 0 && Str::length($model->metaKeywords[0]) > 0) {
                                echo '<i class="fas fa-tags"></i> ';
                                foreach ($model->metaKeywords as $tag) {
                                    $tag = trim($tag);
                                    echo Url::a(['content/tag', [urlencode($tag)]], $tag, ['class' => 'badge bg-secondary']) . "&nbsp;";
                                }
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if (!Str::likeEmpty($model->source)): ?>
            <div id="content-source" style="padding-top: 5px;">
                <?php
                $sourceUrl = $model->source;
                $parseUrl = parse_url($sourceUrl);
                $sourceHost = $parseUrl['host'];
                ?>
                <i class="fas fa-link"></i> <?= __('Source') ?>: <a href="<?= $sourceUrl ?>" rel="nofollow" target="_blank">
                    <?= $sourceHost ?>
                </a>
            </div>
        <?php endif; ?>
    </article>
<?php if ($showComments): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="h3 text-success"><?= __('Comments') ?></div>
            <hr />
            <?= \Widgets\Front\Comments\Comments::widget(['name' => 'content', 'id' => $model->id, 'config' => 'small']); ?>
        </div>
    </div>
<?php endif; ?>
<?php $this->stop() ?>

<?php $this->push('javascript') ?>

<?= \Widgets\Tinymce\Tinymce::widget(['config' => 'small']); ?>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js"></script>

<?php $this->stop() ?>
