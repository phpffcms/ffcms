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
<div class="mb-1 short-comment">
    <div class="row mb-1">
        <div class="col-md-6">
            <i class="fas fa-user"></i>
            <?php if ((int)$comment['user']['id'] > 0): ?>
                <?= Url::a(['profile/show', [$comment['user']['id']]], $comment['user']['name'], ['style' => 'color: #595959']) ?>
            <?php else: ?>
                <?= $comment['user']['name'] ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6 float-end">
            <small class="text-secondary float-end">
                <i class="far fa-calendar"></i> <?= Date::humanize($comment['date']) ?>
            </small>
        </div>
    </div>

    <div class="row">
        <div class="col widget-comment-text">
            <a href="<?= \App::$Alias->baseUrl . '/' . $comment['app_name'] . '/comments/' . $comment['app_id'] ?>">
                <?= Text::cut(\App::$Security->strip_tags($comment['text']), 0, $snippet) ?>
            </a>
        </div>
    </div>
</div>
<?php endforeach; ?>
