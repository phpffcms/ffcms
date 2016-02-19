<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Feedback\FormSettings */

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Settings');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('feedback/index') => __('Feedback'),
    __('Settings')
];

?>

<?= $this->render('feedback/_tabs') ?>
<h1><?= __('Feedback settings') ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '']); ?>

<?= $form->start() ?>

<?= $form->field('guestAdd', 'checkbox', null, __('Allow not authorized users add feedback requests?')) ?>
<?= $form->field('useCaptcha', 'checkbox', null, __('Use captcha on feedback form to prevent spam?')) ?>

<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>