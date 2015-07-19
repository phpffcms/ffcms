<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $model Apps\Model\Admin\Profile\FormFieldUpdate */
/** @var $record object */

$this->title = __('Profile settings');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('profile/index') => __('Profile'),
    __('Settings')
];

?>

<?= $this->render('profile/_tabs') ?>

<h1><?= __('Profile settings') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '', 'method' => 'post']) ?>

<?= $form->field('usersOnPage', 'text', ['class' => 'form-control'], __('How many users will be displayed per one list page?')) ?>
<?= $form->field('guestView', 'checkbox', null, __('Allow guests view user profiles?')) ?>
<?= $form->field('wallPostOnPage', 'text', ['class' => 'form-control'], __('How many wall posts must be displayed per one page?')) ?>
<?= $form->field('delayBetweenPost', 'text', ['class' => 'form-control'], __('Delay between 2 posts on wall from one user in seconds')) ?>
<?= $form->field('rating', 'checkbox', null, __('Enable user rating system?')) ?>
<?= $form->field('ratingDelay', 'text', ['class' => 'form-control'], __('Delay in seconds between repeat change of rating from one user(model: one to one)')) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>