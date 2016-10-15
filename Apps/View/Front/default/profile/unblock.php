<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Url;

/** @var $model Apps\Model\Front\Profile\FormIgnoreDelete */
/** @var $this object */

$this->title = __('Blacklist user remove');

$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    Url::to('profile/show', \App::$User->identity()->getId()) => __('Profile'),
    Url::to('profile/ignore') => __('Blacklist'),
    __('Blacklist cleanup')
];

?>

<?= $this->render('profile/_settingsTab') ?>
<h1><?= __('Remove user from blacklist') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '', 'method' => 'post']) ?>
<?= $form->start() ?>

<div class="row">
    <div class="col-md-3">
        <label class="pull-right"><?= $model->getLabel('name') ?></label>
    </div>
    <div class="col-md-9">
        <?= Simplify::parseUserLink($model->id) ?>
    </div>
</div>

<p><?= __('Are you sure to remove this user from your blacklist?') ?> <?= __('No any attentions will be displayed!') ?></p>

<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Remove'), ['class' => 'btn btn-danger']) ?></div>
<?= $form->finish() ?>