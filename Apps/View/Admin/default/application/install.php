<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Application\FormInstall */
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Install app');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    __('Install')
];
?>
<h1><?= __('Application install'); ?></h1>
<hr />
<p>On this page you can install FFCMS application, using application system name. Please, type app.sys_name in form below.</p>
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']) ?>

<?= $form->field('sysname', 'text', ['class' => 'form-control'], __('Specify your application or widget system name for installation')) ?>

<?= $form->submitButton('Try install', ['class' => 'btn btn-primary']) ?>

<?= $form->finish() ?>

