<?php

use Apps\Model\Api\Comments\EntityCommentData;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Text;
use Ffcms\Templex\Url\Url;

/** @var EntityCommentData[] $comments */
/** @var \Ffcms\Core\Arch\View $this */
/** @var int $snippet */
?>


<?php foreach ($comments as $comment):?>
<div class="short-comment mb-1 pb-1">
    <div class="row">
        <div class="col-md-12">
            <i class="fa fa-user"></i>
            <?php if ((int)$comment['user']['id'] > 0): ?>
                <?= Url::a(['profile/show', [$comment['user']['id']]], $comment['user']['name']) ?>
            <?php else: ?>
                <?= $comment['user']['name'] ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 text-center">
            <img class="img-fluid rounded mt-1" src="<?= $comment['user']['avatar']?>" style="min-height: 50px;" alt="Picture of user <?= $comment['user']['name'] ?>">
        </div>
        <div class="col-md">
            <a href="<?= \App::$Alias->baseUrl . $comment['pathway'] . '#comment-list' ?>">
                <?= Text::cut(\App::$Security->strip_tags($comment['text']), 0, $snippet) . '...' ?>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md">
            <span class="float-right text-secondary">
                <i class="fa fa-calendar"></i> <?= Date::humanize($comment['date']) ?>
            </span>
        </div>
    </div>
</div>
<?php endforeach; ?>
