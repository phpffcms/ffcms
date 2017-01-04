<?php
use Apps\Model\Api\Comments\EntityCommentData;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Url;

/** @var \Apps\ActiveRecord\CommentPost $records */
/** @var object $this */
/** @var int $snippet */

?>

<?php foreach ($records as $comment):?>
<?php
/** @var array $data */
$data = (new EntityCommentData($comment, false))->make();
?>

<ul class="media-list">
	<li class="media">
		<ul class="list-inline list-info">
			<li><i class="glyphicon glyphicon-calendar"></i> <?= Date::humanize($data['date']) ?></li>
			<li><i class="glyphicon glyphicon-user"></i>
			<?php if ((int)$data['user']['id'] > 0): ?>
				<?= Url::link(['profile/show', $data['user']['id']], $data['user']['name']) ?>
			<?php else: ?>
				<?= $data['user']['name'] ?>
			<?php endif; ?>
			</li>
		</ul>
	</li>
	<li class="media">
		<span class="pull-left">
			<img class="media-object img-responsive" src="<?= $data['user']['avatar']?>" style="width: 64px; height: 64px;" alt="Picture of user <?= $data['user']['name'] ?>">
		</span>
		<div class="media-body">
			<a href="<?= \App::$Alias->baseUrl . $data['pathway'] . '#comment-list' ?>">
				<?= \App::$Security->strip_tags($data['text']) ?>
			</a>
		</div>
	</li>
</ul>
<?php endforeach; ?>
