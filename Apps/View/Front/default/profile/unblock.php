<?php

use Ffcms\Core\Helper\Simplify;
use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Front\Profile\FormIgnoreDelete $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Blacklist user remove'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        Url::to('profile/show', [\App::$User->identity()->getId()]) => __('Profile'),
        Url::to('profile/ignore') => __('Blacklist'),
        __('Blacklist cleanup')
    ]
]);
?>

<?php $this->start('body') ?>

<?php $this->insert('profile/menus/settings') ?>

<h1><?= __('Remove user from blacklist') ?></h1>
<hr />
<?php $form = $this->form($model) ?>
<?= $form->start() ?>

    <div class="row">
        <div class="col-md-3">
            <label class="float-right"><?= $model->getLabel('name') ?></label>
        </div>
        <div class="col-md-9">
            <?= Simplify::parseUserLink($model->id) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9 offset-md-3">
            <p><?= __('Are you sure to remove this user from your blacklist?') ?> <?= __('No any attentions will be displayed!') ?></p>
        </div>
    </div>

    <?= $form->button()->submit(__('Remove'), ['class' => 'btn btn-danger']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
