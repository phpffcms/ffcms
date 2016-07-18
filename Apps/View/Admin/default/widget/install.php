<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Application\FormInstall */
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Install widget');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    __('Install')
];
?>
<h1><?= __('Widget install'); ?></h1>
<hr />
<p><?= __('On this page you can install FFCMS widget, using widget system name. Please, type widget.sys_name in form below.') ?></p>
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']) ?>

<?= $form->start() ?>

<?= $form->field('sysname', 'text', ['class' => 'form-control'], __('Specify your widget system name for installation')) ?>

<?= $form->submitButton(__('Try install'), ['class' => 'btn btn-primary']) ?>&nbsp;
<?= Url::link(['widget/index'], __('Cancel'), ['class' => 'btn btn-default']); ?>

<?= $form->finish() ?>

