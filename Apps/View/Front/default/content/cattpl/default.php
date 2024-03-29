<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var array $pagination */
/** @var array $configs */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var array $category */
/** @var Apps\Model\Front\Content\EntityCategoryList $model */

$catConfigs = [];
foreach ($model->category['configs'] as $key=>$value) {
    $catConfigs[$key] = (bool)$value;
}
$catMeta = [
    'date' => $catConfigs['showDate'],
    'author' => $catConfigs['showAuthor'],
    'views' => $catConfigs['showViews']
];

$breads = null;
if (!\App::$Request->isPathInjected()) {
    $breads = [
        Url::to('/') => __('Home'),
        Url::to('content/list') => __('Contents'),
        $model->category['title']
    ];
}

$this->layout('_layouts/default', [
    'title' => $model->category['title'],
    'breadcrumbs' => $breads
]);
?>
<?php $this->start('body') ?>
<script>
    // content id array
    var contentIds = [];
</script>
<?php if (!\App::$Request->isPathInjected()): ?>
    <h1>
        <?= $model->category['title'] ?>
        <?php if (!Str::likeEmpty($model->category['rss'])): ?>
            <small><a href="<?= $model->category['rss'] ?>" target="_blank"><i class="fas fa-rss"></i></a></small>
        <?php endif; ?>
        <div class="float-end">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-sort-amount-up"></i> <?= __('Sorting')?> <span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?= $model->category['sort']['newest'] ?>"><?= __('Newest') ?> </a>
                    <a class="dropdown-item" href="<?= $model->category['sort']['rating'] ?>"><?= __('Rating') ?></a>
                    <a class="dropdown-item" href="<?= $model->category['sort']['views'] ?>"><?= __('Popularity') ?></a>
                </div>
            </div>
        </div>
    </h1>
    <?php if (Str::length($model->category['description']) > 0): ?>
        <p class="text-muted"><?= $model->category['description'] ?></p>
    <?php endif; ?>
    <hr />
<?php endif; ?>
<?php if ($model->getContentCount() < 1): ?>
    <p class="alert alert-warning"><?= __('This category is empty!') ?></p>
    <?php return; endif; ?>
<?php foreach ($model->items as $item): ?>
    <article class="article-item" itemscope="itemscope" itemtype="https://schema.org/NewsArticle">
        <h2 itemprop="name">
            <?php if ($item['important']): ?>
                <i class="fas fa-fire" style="color: #a50000"></i>
            <?php endif; ?>
            <a href="<?= \App::$Alias->baseUrl . $item['uri'] ?>">
                <?= $item['title'] ?>
            </a>
        </h2>
        <?php if (Arr::in(true, $catMeta)): ?>
            <div class="meta">
                <?php if ($catConfigs['showCategory'] === true): ?>
                    <span class="spaced"><i class="fas fa-list"></i>
                        <?= Url::a(
                            ['content/list', [$item['category']->path]],
                            \App::$Translate->getLocaleText($item['category']->title),
                            ['itemprop' => 'genre']
                        ) ?>
            </span>
                <?php endif; ?>
                <?php if ($catConfigs['showDate'] === true): ?>
                    <span class="spaced"><i class="fas fa-calendar"></i>
                <time datetime="<?= date('c', Date::convertToTimestamp($item['date'])) ?>" itemprop="datePublished">
                    <?= $item['date'] ?>
                </time>
            </span>
                <?php endif; ?>
                <?php if ($catConfigs['showAuthor'] === true): ?>
                    <span class="spaced"><i class="fas fa-user"></i>
                        <?php
                        $ownerName = $item['author']->profile === null ? __('Unknown') : $item['author']->profile->getName();
                        if ($item['author']->getId() < 1) {
                            echo '<s>' . $ownerName . '</s>';
                        } else {
                            echo Url::a(['profile/show', [$item['author']->getId()]], $ownerName, ['itemprop' => 'author']);
                        }
                        ?>
            </span>
                <?php endif; ?>
                <?php if ($catConfigs['showViews'] === true): ?>
                    <span class="spaced"><i class="fas fa-eye"></i> <?= $item['views'] ?></span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <hr />
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <?php if ($catConfigs['showPoster'] === true && $item['thumb'] !== null): ?>
                    <img src="<?= \App::$Alias->scriptUrl . $item['thumb'] ?>" class="image_poster img-thumbnail d-none d-sm-block mx-auto" alt="<?= __('Poster for') ?>: <?= Str::lowerCase($item['title']) ?>" />
                <?php endif; ?>
                <div itemprop="text articleBody">
                    <?= $item['text'] ?>
                </div>
            </div>
        </div>
        <div class="meta">
            <?php /**if ((int)$catConfigs['showRating'] === 1) {
                echo \App::$View->render('content/_rate', [
                    'id' => $item['id'],
                    'canRate' => $item['canRate'],
                    'rating' => $item['rating']
                ]);
            }*/ ?>
            <?php if((int)$catConfigs['showComments'] === 1): ?>
            <span class="spaced"><i class="far fa-comment"></i>
                <a href="<?= \App::$Alias->baseUrl . $item['uri'] ?>#comments-list"><?= __('Comments') ?>: <span itemprop="commentCount" id="comment-count-<?= $item['id'] ?>">0</span></a>
            </span>
            <?php else: ?>
            <span class="spaced">&nbsp;</span>
            <?php endif; ?>
            <span class="float-end">
            <?php if ((int)$catConfigs['showTags'] === 1 && $item['tags'] !== null && Any::isArray($item['tags'])): ?>
                <span class="spaced"><i class="fas fa-tags"></i>
                    <?php
                    foreach ($item['tags'] as $tag) {
                        $tag = trim($tag);
                        echo Url::a(['content/tag', [urlencode($tag)]], $tag, ['class' => 'badge bg-secondary']) . "&nbsp;";
                    }
                    ?>
                </span>
                <meta itemprop="keywords" content="<?php implode(',', $item['tags']); ?>">
            <?php endif; ?>
            </span>
        </div>
    </article>
    <script>
        contentIds.push(<?= $item['id'] ?>);
    </script>
<?php endforeach; ?>

<?php //\App::$View->render('content/_rateJs') ?>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<script>
    $(document).ready(function() {
        if (typeof contentIds === 'object' || contentIds.length > 0) {
            $.getJSON(script_url + '/api/comments/count/content?lang='+script_lang, {id: contentIds}, function(json){
                // check if response is success
                if (json.status !== 1 || typeof json.count !== 'object') {
                    return null;
                }
                // list response json counts by itemId => count
                for (var itemId in json.count) {
                    $('#comment-count-' + itemId).text(json.count[itemId]);
                }
            });
        }
    })
</script>
<?php $this->stop() ?>