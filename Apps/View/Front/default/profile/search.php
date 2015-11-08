<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var $this object */
/** @var $model Apps\Model\Front\Profile\FormUserSearch */
/** @var $records object */
/** @var $pagination object */
/** @var $ratingOn int */

$this->title = __('User list') . ': ' . __('Search');

$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    $this->title
];

?>

<h1><?= $this->title ?></h1>
<?= $this->render('profile/_listTab', ['rating' => $ratingOn]) ?>
<br />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '']) ?>

<?= $form->field('query', 'text', ['class' => 'form-control'], __('Enter user nickname or part of user nickname, more then 3 characters')) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Search'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>
<?php
if ($records === null || $records->count() < 1) {
    if ($model->send()) {
        echo '<div class="row"><div class="col-md-12"><div class="alert alert-danger">' . __('Users are not founded!') . '</div></div></div>';
    }

    return;
}
?>
<?php foreach ($records as $profile) :?>
    <div class="row" style="padding-top: 10px;">
        <div class="col-md-2">
            <div class="text-center"><img src="<?= $profile->getAvatarUrl('small') ?>" class="img-responsive img-circle img-thumbnail"/></div>
        </div>
        <div class="col-md-8">
            <h3>
                <?= Url::link(['profile/show', $profile->user_id], Str::likeEmpty($profile->nick) ? __('No name') : $profile->nick) ?>
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