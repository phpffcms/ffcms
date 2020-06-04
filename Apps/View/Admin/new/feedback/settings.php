<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Feedback\FormSettings $model */

$this->layout('_layouts/default', [
    'title' => __('Settings'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('feedback/index') => __('Feedback'),
        __('Settings')
    ]
]);

?>

<?php $this->start('body') ?>

<?= $this->insert('feedback/_tabs') ?>

<h1><?= __('Feedback settings') ?></h1>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->boolean('guestAdd', null, __('Allow not authorized users add feedback requests?')) ?>
<?= $form->fieldset()->boolean('useCaptcha', null, __('Use captcha on feedback form to prevent spam?')) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
