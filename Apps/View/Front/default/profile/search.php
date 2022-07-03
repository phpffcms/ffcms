<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Front\Profile\FormUserSearch $model */
/** @var \Apps\ActiveRecord\Profile[]|\Illuminate\Support\Collection|null $records */
/** @var array|null $pagination */
/** @var bool $ratingOn */

$title = __('User list') . ': ' . __('Search');
$this->layout('_layouts/default', [
    'title' => $title,
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        $title
    ]
]);
?>
<?php $this->start('body') ?>

<h1><?= $title ?></h1>

<?php $this->insert('profile/menus/index', ['rating' => $ratingOn]) ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('query', null, __('Enter user name or part of user name, more then 3 characters')) ?>
<?= $form->button()->submit(__('Search'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php
if (!$records || $records->count() < 1) {
    if ($model->send()) {
        echo '<div class="row"><div class="col-md-12"><div class="alert alert-danger">' . __('Users are not founded!') . '</div></div></div>';
    }
    $this->stop();
    return;
}
?>
<?php foreach ($records as $profile) :?>
    <div class="row" style="padding-top: 10px;">
        <div class="col-md-2">
            <div class="text-center"><img src="<?= $profile->getAvatarUrl('small') ?>" class="img-fluid img-thumbnail"/></div>
        </div>
        <div class="col-md-8">
            <h3>
                <?= Url::a(['profile/show', [$profile->user_id]], Str::likeEmpty($profile->name) ? __('No name') : $profile->name) ?>
            </h3>
            <p><?= __('Registered') ?>: <?= Date::convertToDatetime($profile->created_at, Date::FORMAT_TO_DAY) ?></p>
        </div>
        <div class="col-md-2">
            <h3 class="float-end">
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
            </h3>
        </div>
    </div>
    <hr/>
<?php endforeach; ?>

<?php if ($pagination) {
    echo $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
        ->size($pagination['total'], $pagination['page'], $pagination['step'])
        ->display();
} ?>

<?php $this->stop() ?>