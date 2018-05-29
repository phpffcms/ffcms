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
    var contentItemList = {path: {}}
</script>
<?php if (!\App::$Request->isPathInjected()): ?>
    <h1>
        <?= $model->category['title'] ?>
        <?php if (!Str::likeEmpty($model->category['rss'])): ?>
            <small><a href="<?= $model->category['rss'] ?>" target="_blank"><i class="fa fa-rss"></i></a></small>
        <?php endif; ?>
        <div class="float-right">
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-sort-amount-desc"></i> <?= __('Sorting')?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="<?= $model->category['sort']['newest'] ?>"><?= __('Newest') ?> </a></li>
                    <li><a href="<?= $model->category['sort']['rating'] ?>"><?= __('Rating') ?></a></li>
                    <li><a href="<?= $model->category['sort']['views'] ?>"><?= __('Popularity') ?></a></li>
                </ul>
            </div>
        </div>
    </h1>
    <?php if (Str::length($model->category['description']) > 0): ?>
        <p><?= $model->category['description'] ?></p>
    <?php endif; ?>
    <hr />
<?php endif; ?>
<?php if ($model->getContentCount() < 1): ?>
    <p class="alert alert-warning"><?= __('This category is empty!') ?></p>
    <?php return; endif; ?>
<?php foreach ($model->items as $item): ?>
    <article class="article-item" itemscope="itemscope" itemtype="https://schema.org/NewsArticle">
        <h2 itemprop="name">
            <a href="<?= \App::$Alias->baseUrl . $item['uri'] ?>">
                <?= $item['title'] ?>
            </a>
            <?php if ($item['important']): ?>
                <i class="fa fa-fire"></i>
            <?php endif; ?>
        </h2>
        <?php if (Arr::in(true, $catMeta)): ?>
            <div class="meta">
                <?php if ($catConfigs['showCategory'] === true): ?>
                    <span class="spaced"><i class="fa fa-list"></i>
                        <?= Url::a(
                            ['content/list', [$item['category']->path]],
                            \App::$Translate->getLocaleText($item['category']->title),
                            ['itemprop' => 'genre']
                        ) ?>
            </span>
                <?php endif; ?>
                <?php if ($catConfigs['showDate'] === true): ?>
                    <span class="spaced"><i class="fa fa-calendar"></i>
                <time datetime="<?= date('c', Date::convertToTimestamp($item['date'])) ?>" itemprop="datePublished">
                    <?= $item['date'] ?>
                </time>
            </span>
                <?php endif; ?>
                <?php if ($catConfigs['showAuthor'] === true): ?>
                    <span class="spaced"><i class="fa fa-user"></i>
                        <?php
                        $ownerNick = $item['author']->profile === null ? __('Unknown') : $item['author']->profile->getNickname();
                        if ($item['author']->getId() < 1) {
                            echo '<s>' . $ownerNick . '</s>';
                        } else {
                            echo Url::a(['profile/show', [$item['author']->getId()]], $ownerNick, ['itemprop' => 'author']);
                        }
                        ?>
            </span>
                <?php endif; ?>
                <?php if ($catConfigs['showViews'] === true): ?>
                    <span class="spaced"><i class="fa fa-eye"></i> <?= $item['views'] ?></span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <hr />
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <?php if ($catConfigs['showPoster'] === true && $item['thumb'] !== null): ?>
                    <img src="<?= \App::$Alias->scriptUrl . $item['thumb'] ?>" class="image_poster img-thumbnail d-none d-sm-block" alt="<?= __('Poster for') ?>: <?= Str::lowerCase($item['title']) ?>" />
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

            <span class="spaced"><i class="fa fa-comment-o"></i>
                <a href="<?= \App::$Alias->baseUrl . $item['uri'] ?>#comments-list"><?= __('Comments') ?>: <span itemprop="commentCount" id="comment-count-<?= $item['id'] ?>">0</span></a>
            </span>
            <span class="float-right">
            <?php if ((int)$catConfigs['showTags'] === 1 && $item['tags'] !== null && Any::isArray($item['tags'])): ?>
                <span class="spaced"><i class="fa fa-tags"></i>
                    <?php
                    foreach ($item['tags'] as $tag) {
                        $tag = trim($tag);
                        echo Url::a(['content/tag', [urlencode($tag)]], $tag, ['class' => 'badge badge-secondary']) . "&nbsp;";
                    }
                    ?>
                </span>
                <meta itemprop="keywords" content="<?php implode(',', $item['tags']); ?>">
            <?php endif; ?>
            </span>
        </div>
    </article>
    <script>
        contentItemList['path'][<?= $item['id'] ?>] = '<?= $item['uri'] ?>';
    </script>
<?php endforeach; ?>

<?php //\App::$View->render('content/_rateJs') ?>

<?= $this->bootstrap()->pagination(['content/my'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<script>
    $(document).ready(function() {
        if (typeof contentItemList === 'object' || Ojbect.keys(contentItemList).length > 0) {
            $.getJSON(script_url + '/api/comments/count?lang='+script_lang, contentItemList, function(json){
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