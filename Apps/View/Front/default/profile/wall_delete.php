<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var $post \Apps\ActiveRecord\WallPost */
/** @var $model \Apps\Model\Front\Profile\FormWallPostDelete */
/** @var \Ffcms\Templex\Template\Template $this */


$this->layout('_layouts/default', [
    'title' => __('Delete post'),
    'breadcrumbs' => [
        Url::to('/') => __('Home'),
        Url::to('profile/show', [App::$User->identity()->getId()]) => __('Profile'),
        __('Delete post')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Delete wall post') ?></h1>
<hr />
<p><?= __('Are you sure to delete this post? No more attention will be displayed!') ?></p>
<div class="row" id="wall-post-<?= $post->id ?>">
    <div class="col-md-2">
        <div class="text-center">
            <img class="img-fluid img-rounded" alt="Avatar of <?= $post->senderUser->profile->getNickname() ?>" src="<?= $post->senderUser->profile->getAvatarUrl('small') ?>" />
        </div>
    </div>
    <div class="col-md-10">
        <h5 style="margin-top: 0;">
            <i class="fa fa-pencil"></i> <?= Url::a(['profile/show', [$post->sender_id]], $post->senderUser->profile->getNickname()) ?>
            <small class="float-right"><?= Date::convertToDatetime($post->updated_at, Date::FORMAT_TO_SECONDS); ?></small>
        </h5>
        <div class="wall-post-text">
            <?= \App::$Security->strip_tags($post->message); ?>
        </div>
     </div>
</div>

<?php $form = $this->form($model); ?>
<?= $form->start() ?>
<?= $form->field()->hidden('id'); ?>
<div class="row mt-1">
    <div class="col-md-12">
        <input type="submit" name="<?= $model->getFormName() ?>[submit]" value="<?=__('Delete')?>" class="btn btn-danger" />
    </div>
</div>
<?= $form->stop() ?>

<?php $this->stop() ?>
