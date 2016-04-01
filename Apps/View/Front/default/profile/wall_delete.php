<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $post \Apps\ActiveRecord\WallPost */
/** @var $model \Apps\Model\Front\FormWallPostDelete */
/** @var $this \Ffcms\Core\Arch\View */

$this->title = __('Delete post');

$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    Url::to('profile/index') => __('Users'),
    __('Delete post')
];

?>
<h1><?= __('Delete wall post') ?></h1>
<hr />
<p><?= __('Are you sure to delete this post? No more attention will be displayed!') ?></p>
<?php
/** @var $referObject object */
$referObject = \App::$User->identity($post->sender_id);
$referNickname = ($referObject->getProfile()->nick == null ? __('No name') : \App::$Security->strip_tags($referObject->getProfile()->nick));
?>
<div class="row wall-post" id="wall-post-<?= $post->id ?>">
    <div class="col-md-2">
        <div class="text-center"><img class="img-responsive img-rounded" alt="Avatar of <?= $referNickname ?>"
                                      src="<?= $referObject->getProfile()->getAvatarUrl('small') ?>" />
        </div>
    </div>
    <div class="col-md-10">
        <h5 style="margin-top: 0;">
            <i class="fa fa-pencil"></i> <?= Url::link(['profile/show', $post->sender_id], $referNickname) ?>
            <small class="pull-right"><?= Date::convertToDatetime($post->updated_at, Date::FORMAT_TO_SECONDS); ?></small>
        </h5>
        <div class="wall-post-text">
            <?= \App::$Security->strip_tags($post->message); ?>
        </div>
    </div>
</div>

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']); ?>
<?= $form->start() ?>
<?= $form->field('id', 'hidden'); ?>
<div class="col-md-offset-2 col-md-10"><?= $form->submitButton(__('Delete'), ['class' => 'btn btn-danger']) ?></div>
<?= $form->finish(false) ?>