<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Main\FormYandexConnect $model */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Yandex.metrika - connection'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        __('Yandex metrika - step 1')
    ]
]);

?>

<?php $this->start('body'); ?>

<h1><?= __('Connect yandex metrika - step 1') ?></h1>
<p><?= __('To make successful connection to yandex.api you should follow next steps:') ?></p>
<?php $form = $this->form($model) ?>
<?= $form->start() ?>
<ol class="ml-2">
    <li><?= __('Create yandex metrika counter') ?>. <?= Url::a(['https://metrika.yandex.ru'], 'Yandex metrika', ['target' => '_blank']) ?></li>
    <li>
        <?= __('Create yandex oauth application and put application ID in form down') ?>.
        <?= __('On yandex website in section general check "Web-service", in section "Yandex.Metrika" check option "Get statistics, read params".') ?>.
        <?= Url::a(['https://oauth.yandex.ru/client/new'], __('Create new yandex oauth app'), ['target' => '_blank']) ?> <br />
        <?= __('Set callback uri #1 to (use https or fail):') ?>
        <?= $form->fieldset()->text('callback', ['disabled' => 'disabled']) ?>
    </li>
    <li>
        <?= __('Input created application ID (name) there (not a secret password)') ?>:
        <?= $form->fieldset()->text('appid') ?>
        <div class="col-md-9 offset-md-3"><?= $form->button()->submit(__('Update'), ['class' => 'btn btn-primary']) ?></div>
    </li>
    <li><?= __('After create application - initialize token click button below') ?>.</li>
</ol>

<?php if ($model->appid && !\Ffcms\Core\Helper\Type\Any::isEmpty($model->appid)): ?>
    <?= $this->bootstrap()->button('a', __('Get Yandex.Metrika token'), [
        'href' => 'https://oauth.yandex.ru/authorize?response_type=token&client_id=' . $model->appid . '&redirect_uri=' . Url::to('main/yandextoken'),
        'class' => 'btn-success'
    ]) ?>
<?php else: ?>
    <?= $this->bootstrap()->alert('warning', __('Create oauth application before get token')) ?>
<?php endif; ?>

<?= $form->stop() ?>

<?php $this->stop();


