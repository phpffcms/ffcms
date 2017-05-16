<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var $id string */
/** @var $add string|null */
/** @var $this \Ffcms\Core\Arch\View */
/** @var $records object */
/** @var $pagination object */
/** @var $ratingOn int */

$this->title = __('User list');

if ($id === 'all') {
    $this->title .= ': ' . __('All');
} elseif ($id === 'rating') {
    $this->title .= ': ' . __('Rating');
} elseif ($id === 'city') {
    $this->title .= ': ' . __('City') . ' ' . \App::$Security->strip_tags($add);
} elseif ($id === 'hobby') {
    $this->title .= ': ' . __('Hobby') . ' ' . \App::$Security->strip_tags($add);
}

$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    $this->title
];

?>

<h1><?= $this->title ?></h1>
<?= $this->render('profile/_listTab', ['rating' => $ratingOn]) ?>

<?php
    if ($records === null || $records->count() < 1) {
        echo '<div class="alert alert-danger">' . __('Users are not founded!') . '</div>';
        return;
    }
?>

<?php foreach ($records as $profile) :?>
    <?php /** @var \Apps\ActiveRecord\Profile $profile */ ?>
    <div class="row" style="padding-top: 10px">
        <div class="col-md-2">
            <div class="text-center"><img src="<?= $profile->getAvatarUrl('small') ?>" class="img-responsive img-circle img-thumbnail"/></div>
        </div>
        <div class="col-md-8">
            <h3>
                <?= Url::link(
                    ['profile/show', $profile->user_id],
                    (Str::likeEmpty($profile->nick) ? __('No name') . '(id' . $profile->user_id . ')' : $profile->nick),
                    ['style' => 'color: ' . $profile->User()->getRole()->color]
                ) ?>
            </h3>
            <p><?= __('Registered') ?>: <?= Date::convertToDatetime($profile->created_at, Date::FORMAT_TO_DAY) ?></p>
            <?php if (\App::$User->identity() !== null && $profile->user_id !== \App::$User->identity()->getId()): ?>
                <?= Url::link(['profile/messages', null, null, ['newdialog' => $profile->user_id]], '<i class="glyphicon glyphicon-envelope"></i> '  . __('New message'), ['class' => 'btn btn-info']) ?>
            <?php endif; ?>
        </div>
        <div class="col-md-2">
            <div class="h3 pull-right">
                <?php
                    $markLabel = 'badge';
                    if ($profile->rating > 0) {
                        $markLabel = 'alert-success';
                    } elseif ($profile->rating < 0) {
                        $markLabel = 'alert-danger';
                    }
                ?>
                <span class="label <?= $markLabel ?>">
                    <?= $profile->rating > 0 ? '+' : null ?><?= $profile->rating ?>
                </span>
            </div>
        </div>
    </div>
    <hr/>
<?php endforeach; ?>


<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>