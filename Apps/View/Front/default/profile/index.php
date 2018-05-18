<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var $id string */
/** @var string|null $add */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\Profile[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */
/** @var bool $ratingOn */

$title = __('User list');
if ($id === 'all') {
    $title .= ': ' . __('All');
} elseif ($id === 'rating') {
    $title .= ': ' . __('Rating');
} elseif ($id === 'city') {
    $title .= ': ' . __('City') . ' ' . $this->e(urldecode($add));
} elseif ($id === 'hobby') {
    $title .= ': ' . __('Hobby') . ' ' . $this->e(urldecode($add));
}

$this->layout('_layouts/default', [
    'title' => $title,
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        $title
    ]
]);

?>

<?php $this->start('body') ?>

<?php $this->insert('profile/menus/index', ['rating' => $ratingOn]) ?>

<?php
if (!$records || $records->count() < 1) {
    echo $this->bootstrap()->alert('danger', __('Users are not founded!'));
    $this->stop();
    return;
}
?>

<?php foreach ($records as $profile) :?>
    <div class="row pt-1">
        <div class="col-md-2">
            <div class="text-center"><img src="<?= $profile->getAvatarUrl('small') ?>" class="img-fluid img-circle img-thumbnail"/></div>
        </div>
        <div class="col-md-8">
            <h3>
                <?= Url::a(
                    ['profile/show', [$profile->user_id]],
                    (Str::likeEmpty($profile->nick) ? __('No name') . '(id' . $profile->user_id . ')' : $profile->nick),
                    ['style' => 'color: ' . $profile->user->role->color]
                ) ?>
            </h3>
            <p><?= __('Registered') ?>: <?= Date::convertToDatetime($profile->created_at, Date::FORMAT_TO_DAY) ?></p>
            <?php if (\App::$User->identity() !== null && $profile->user_id !== \App::$User->identity()->getId()): ?>
                <?= Url::a(['profile/messages', null, ['newdialog' => $profile->user_id]], '<i class="fa fa-envelope"></i> '  . __('New message'), ['class' => 'btn btn-info', 'html' => true]) ?>
            <?php endif; ?>
        </div>
        <div class="col-md-2">
            <div class="h3 float-right">
                <?php
                $markLabel = 'badge-light';
                if ($profile->rating > 0) {
                    $markLabel = 'badge-success';
                } elseif ($profile->rating < 0) {
                    $markLabel = 'badge-danger';
                }
                ?>
                <span class="badge <?= $markLabel ?>">
                    <?= $profile->rating > 0 ? '+' : null ?><?= $profile->rating ?>
                </span>
            </div>
        </div>
    </div>
    <hr/>
<?php endforeach; ?>

<?= $this->bootstrap()->pagination(['profile/index', [$id]], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<?php $this->stop() ?>