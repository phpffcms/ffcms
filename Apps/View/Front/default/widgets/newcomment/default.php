<?php
use Apps\Model\Api\Comments\EntityCommentData;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Url;

/** @var EntityCommentData[] $comments */
/** @var \Ffcms\Core\Arch\View $this */
/** @var int $snippet */

?>

<?php foreach ($comments as $comment):?>
<ul class="media-list">
	<li class="media">
		<ul class="list-inline list-info">
			<li><i class="glyphicon glyphicon-calendar"></i> <?= Date::humanize($comment['date']) ?></li>
			<li><i class="glyphicon glyphicon-user"></i>
			<?php if ((int)$comment['user']['id'] > 0): ?>
				<?= Url::link(['profile/show', $comment['user']['id']], $comment['user']['name']) ?>
			<?php else: ?>
				<?= $comment['user']['name'] ?>
			<?php endif; ?>
			</li>
		</ul>
	</li>
	<li class="media">
		<span class="pull-left">
			<img class="media-object img-responsive" src="<?= $comment['user']['avatar']?>" style="width: 64px; height: 64px;" alt="Picture of user <?= $comment['user']['name'] ?>">
		</span>
		<div class="media-body">
			<a href="<?= \App::$Alias->baseUrl . $comment['pathway'] . '#comment-list' ?>">
				<?= Text::cut(\App::$Security->strip_tags($comment['text']), 0, $snippet) . '...' ?>
			</a>
		</div>
	</li>
</ul>
<?php endforeach; ?>
