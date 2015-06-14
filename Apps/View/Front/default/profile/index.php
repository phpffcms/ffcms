<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
use Ffcms\Core\Helper\Url;

/** @var $add string|null */
/** @var $this object */
/** @var $records object */
/** @var $pagination object */

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
<?= $this->show('profile/_listTab') ?>

<?php
    if ($records === null || $records->count() < 1) {
        echo '<div class="alert alert-danger">' . __('Users are not founded!') . '</div>';
        return;
    }
?>

<?php foreach ($records as $profile) :?>
    <div class="row" style="padding-top: 10px">
        <div class="col-md-2">
            <div class="text-center"><img src="<?= $profile->getAvatarUrl('small') ?>" class="img-responsive img-circle img-thumbnail"/></div>
        </div>
        <div class="col-md-8">
            <h3>
                <?= Url::link(['profile/show', $profile->user_id], $profile->nick === null ? __('No name') : $profile->nick) ?>
            </h3>
            <p><?= __('Registered') ?>: <?= Date::convertToDatetime($profile->created_at, Date::FORMAT_TO_DAY) ?></p>
        </div>
        <div class="col-md-2">
            <h3 class="pull-right">
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
            </h3>
        </div>
    </div>
    <hr/>
<?php endforeach; ?>


<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>