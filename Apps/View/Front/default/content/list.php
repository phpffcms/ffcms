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
/** @var $model Apps\Model\Front\Content\EntityCategoryList */

$catConfigs = [];
foreach ($model->category['configs'] as $key=>$value) {
    $catConfigs[$key] = (int)$value === 1;
}

$catMeta = [
    'date' => $catConfigs['showDate'],
    'author' => $catConfigs['showAuthor'],
    'views' => $catConfigs['showViews']
];

$this->title = $model->category['title'];
if (!\App::$Request->isPathInjected()) {
    $this->breadcrumbs = [
            Url::to('/') => __('Home'),
            Url::to('content/list') => __('Contents'),
            $model->category['title']
    ];
}

?>
<script>
// content id array
var contentItemList = {path: {}}
</script>
<?php if (!\App::$Request->isPathInjected()): ?>
    <h1>
    	<?= $model->category['title'] ?>
    	<?php if ($model->category['rss'] !== false && !Str::likeEmpty($model->category['rss'])): ?>
    		<small class="pull-right"><a href="<?= $model->category['rss'] ?>" target="_blank"><i class="fa fa-rss"></i></a></small>
    	<?php endif; ?>
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
        </h2>
        <?php if (Arr::in(true, $catMeta)): ?>
        <div class="meta">
            <?php if ($catConfigs['showCategory'] === true): ?>
            <span class="spaced"><i class="fa fa-list"></i>
                <?= Url::link(
                    ['content/list', $item['category']->path],
                    Serialize::getDecodeLocale($item['category']->title),
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
                $ownerNick = $item['author']->getProfile()->getNickname();
                if ($item['author']->getId() < 1) {
                    echo '<s>' . $ownerNick . '</s>';
                } else {
                    echo Url::link(['profile/show', $item['author']->getId()], $ownerNick, ['itemprop' => 'author']);
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
                <img src="<?= \App::$Alias->scriptUrl . $item['thumb'] ?>" class="image_poster img-thumbnail hidden-xs" alt="<?= __('Poster for') ?>: <?= Str::lowerCase($item['title']) ?>" />
                <?php endif; ?>
                <div itemprop="text articleBody">
                    <?= $item['text'] ?>
                </div>
            </div>
        </div>
        <div class="meta">
        	<?php if ((int)$catConfigs['showRating'] === 1) {
        	    echo \App::$View->render('content/_rate', [
        	        'id' => $item['id'],
        	        'canRate' => $item['canRate'],
        	        'rating' => $item['rating']
        	    ]);
        	} ?>
        	
        	<span class="spaced hidden-xs"><i class="fa fa-comments"></i>
                <a href="#"><?= __('Comments') ?>: <span itemprop="commentCount" id="comment-count-<?= $item['id'] ?>">0</span></a>
            </span>
            <span class="pull-right">
            <?php if ((int)$configs['keywordsAsTags'] === 1 && $item['tags'] !== null && Obj::isArray($item['tags'])): ?>
                <span class="spaced"><i class="fa fa-tags hidden-xs"></i>
                <?php
                    foreach ($item['tags'] as $tag) {
                        $tag = \App::$Security->strip_tags(trim($tag));
                        echo Url::link(['content/tag', $tag], $tag, ['class' => 'label label-default']) . "&nbsp;";
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

<?= \App::$View->render('content/_rateJs') ?>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>

<script>
window.jQ.push(function() {
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