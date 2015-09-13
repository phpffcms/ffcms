<?php

use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Object;
use Ffcms\Core\Helper\Type\String;
use \Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Url;

/** @var $configs array */
/** @var $this object */
/** @var $category array */
/** @var $model Apps\Model\Front\Content\EntityCategoryRead */

var_dump($model->categoryData['configs']);

$this->title = $model->categoryData['title'];
?>
<h1><?= $model->categoryData['title'] ?></h1>
<?php if (String::length($model->categoryData['description']) > 0): ?>
<p><?= $model->categoryData['description'] ?></p>
<?php endif; ?>
<hr />
<?php foreach ($model->items as $item): ?>
    <article class="article-item" itemscope="itemscope" itemtype="https://schema.org/NewsArticle">
        <h2 itemprop="name">
            <a href="<?= \App::$Alias->scriptUrl . $item['uri'] ?>">
                <?= $item['title'] ?>
            </a>
        </h2>

        <div class="meta">
            <span><i class="fa fa-list"></i>
                <?= Url::link(
                    ['content/list', $item['category']['path']],
                    Serialize::getDecodeLocale($item['category']['title']),
                    ['itemprop' => 'genre']
                ) ?>
            </span>
            <span><i class="fa fa-calendar"></i>
                <time datetime="<?= date('c', Date::convertToTimestamp($item['date'])) ?>" itemprop="datePublished">
                    <?= $item['date'] ?>
                </time>
            </span>
            <span><i class="fa fa-user"></i>
                <?= Url::link(['profile/show', $item['author']->getId()], $item['author']->getProfile()->nick, ['itemprop' => 'author']) ?>
            </span>
            <span><i class="fa fa-eye"></i> <?= $item['views'] ?></span>
        </div>
        <div class="row">
            <div class="col-md-12">
                <img src="<?= \App::$Alias->scriptUrl . $item['thumb'] ?>" class="image_poster img-thumbnail" alt="<?= __('Poster for') ?>: <?= String::lowerCase($item['title']) ?>" />
                <div itemprop="text articleBody">
                    <?= $item['text'] ?>
                </div>
            </div>
        </div>
        <div class="meta">
            <?php if ($item['tags'] !== null && Object::isArray($item['tags'])): ?>
            <span><i class="fa fa-tags"></i>
                <?php
                    foreach ($item['tags'] as $tag) {
                        $tag = \App::$Security->strip_tags(trim($tag));
                        echo Url::link(['content/tag', $tag], $tag, ['class' => 'label label-default']) . "&nbsp;";
                    }
                ?>
            </span>
            <meta itemprop="keywords" content="<?php implode(',', $item['tags']); ?>">
            <?php endif; ?>
            <span><i class="fa fa-comments"></i> <a
                    href="http://oncrimea.ru/ru/news/politics/ministram-krima-porekomendovali-ezdit-v-marshrutkah-i-uznavat-mneniya-grazhdan.html#comment_load">Комментарии
                    : <span itemprop="commentCount">0</span></a></span>
            <span class="pull-right hidden-xs">
                <i class="fa fa-share"></i><a
                    href="http://oncrimea.ru/ru/news/politics/ministram-krima-porekomendovali-ezdit-v-marshrutkah-i-uznavat-mneniya-grazhdan.html"
                    itemprop="url">Подробней</a>
            </span>
        </div>
    </article>

<?php endforeach; ?>