<?php

/** @var $model Apps\Model\Front\Content\EntityContentRead */
/** @var $this object */
/** @var $trash bool */
/** @var $configs array */
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

// set meta title
$this->title = \App::$Security->strip_tags($model->metaTitle);
if (Str::likeEmpty($this->title)) {
    $this->title = \App::$Security->strip_tags($model->title);
}
// set meta description
$this->description = \App::$Security->strip_tags($model->metaDescription);
// set meta keywords
if (Obj::isArray($model->metaKeywords) && count($model->metaKeywords) > 0) {
    $this->keywords = implode(', ', $model->metaKeywords);
}

// don't use breadcrumbs on injected pathway rule
if (!\App::$Request->isPathInjected()) {
    $breadcrumbs = [
            Url::to('/') => __('Home')
    ];
    if (Obj::isArray($model->catNesting)) {
        foreach ($model->catNesting as $cat) {
            if ($cat['path'] === '') {
                $breadcrumbs[Url::to('content/list', $cat['path'])] = __('Contents');
            } else {
                $breadcrumbs[Url::to('content/list', $cat['path'], null, [], false)] = $cat['name'];
            }
        }
        $breadcrumbs[] = __('Content') . ': ' . Str::substr($this->title, 0, 40);
    }

    $this->breadcrumbs = $breadcrumbs;
}
?>

<?php
$properties = [
    'date' => (int)$model->getCategory()->getProperty('showDate') === 1,
    'author' => (int)$model->getCategory()->getProperty('showAuthor') === 1,
    'views' => (int)$model->getCategory()->getProperty('showViews') === 1,
    'category' => (int)$model->getCategory()->getProperty('showCategory') === 1
];
$showComments = (int)$model->getCategory()->getProperty('showComments') === 1;
$showPoster = (int)$model->getCategory()->getProperty('showPoster') === 1;

\App::$Cache->set('test.me.baby.1', ['key' => 'value']);
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
    <div id="content-text">
        <?php if ($showPoster === true && $model->posterFull !== null && $model->posterThumb !== null): ?>
            <a href="#showPoster" data-toggle="modal" data-target="#showPoster">
                <img alt="<?= __('Poster for') ?>: <?= Str::lowerCase(\App::$Security->strip_tags($model->title)) ?>" src="<?= \App::$Alias->scriptUrl . $model->posterThumb ?>" class="image_poster img-thumbnail" />
            </a>
            <!-- Modal poster pop-up -->
            <div class="modal fade" id="showPoster" tabindex="-1" role="dialog" aria-labelledby="showPosterModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel"><?= __('View poster') ?></h4>
                        </div>
                        <div class="modal-body">
                            <img class="img-responsive" src="<?= \App::$Alias->scriptUrl . $model->posterFull ?>" alt="<?= __('Poster image') ?>" style="margin: 0 auto;" />
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ;?>
        <?= $model->text ?>
    </div>
    <?php if ($model->galleryItems !== null && Obj::isArray($model->galleryItems)): ?>
        <div class="row">
        <?php foreach ($model->galleryItems as $thumbPic => $fullPic): ?>
            <div class="col-md-2 well">
                <a href="#showGallery" class="modalGallery" content="<?= \App::$Alias->scriptUrl . $fullPic ?>"><img src="<?= \App::$Alias->scriptUrl . $thumbPic ?>" class="img-responsive image-item" /></a>
            </div>
        <?php endforeach; ?>
        </div>
        <div class="modal fade" id="showGallery" tabindex="-1" role="dialog" aria-labelledby="showshowGallery">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="showModalLabel"><?= __('View picture') ?></h4>
                    </div>
                    <div class="modal-body" id="modal-gallery-body">
                        <img class="img-responsive" src="<?= \App::$Alias->scriptUrl . $model->posterFull ?>" alt="<?= __('Gallery picture') ?>" style="margin: 0 auto;" />
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if ((int)$configs['keywordsAsTags'] === 1): ?>
    <div id="content-tags">
        <?php
        if (Obj::isArray($model->metaKeywords) && count($model->metaKeywords) > 0 && Str::length($model->metaKeywords[0]) > 0) {
            echo '<i class="fa fa-tags"></i>';
            foreach ($model->metaKeywords as $tag) {
                $tag = \App::$Security->strip_tags(trim($tag));
                echo Url::link(['content/tag', $tag], $tag, ['class' => 'label label-default']) . "&nbsp;";
            }
        }
        ?>
    </div>
    <?php endif; ?>
    <?php if (!Str::likeEmpty($model->source)): ?>
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
<div class="row">
    <div class="col-md-12">
        <h3><?= __('Comments') ?></h3>
        <?= \Widgets\Front\Comments\Comments::widget(['targetClass' => 'wysi-comments', 'config' => 'config-small']); ?>
    </div>
</div>
<?php if ($model->galleryItems !== null && Obj::isArray($model->galleryItems)): ?>
<script>
    window.jQ.push(function(){
        $('.modalGallery').on('click', function() {
            var picture = $(this).attr('content');
            if (picture != null && picture.length > 0) {
                $('#modal-gallery-body').html('<img class="img-responsive" alt="Picture" style="margin: 0 auto;" src="' + picture + '"/>');
                $('#showGallery').modal('show');
            }
        });
    });
</script>
<?php endif; ?>