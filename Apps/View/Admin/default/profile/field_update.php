<?php

use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var $model Apps\Model\Admin\Profile\FormFieldUpdate */
/** @var $record object */

$this->title = __('Manage field');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('profile/index') => __('Profile list'),
    Url::to('profile/fieldlist') => __('Profile fields'),
    __('Manage field')
];

?>

<?= $this->render('profile/_tabs') ?>

<h1><?= __('Manage addition field') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '', 'method' => 'post']) ?>

<?= $form->start() ?>

<?php
$nameTab = [];
foreach (\App::$Properties->get('languages') as $lang) {
    $nameTab[] = [
        'type' => 'tab',
        'text' => __('Lang') . ': ' . Str::upperCase($lang),
        'content' => $form->field('name.' . $lang, 'text', ['class' => 'form-control'], __('Define field name, which be displayed for user for current language locale')),
        'active' => $lang === \App::$Request->getLanguage(),
        'html' => true,
        '!secure' => true
    ];
}
?>

<?= Nav::display([
    'property' => ['class' => 'nav-pills'],
    'blockProperty' => ['class' => 'nav-locale-block'],
    'tabAnchor' => 'fieldlist',
    'items' => $nameTab
]); ?>

<?= $form->field('type', 'select', ['class' => 'form-control', 'options' => ['text', 'link']], __('Select type of additional field')) ?>
<?= $form->field('reg_exp', 'text', ['class' => 'form-control'], __('Set regular expression to validate input data from user for this field. Example: /^[0-9]/*$')) ?>
<?= $form->field(
    'reg_cond', 'select',
    ['class' => 'form-control', 'options' => ['0' => __('Reverse (exclude) condition'), '1' => __('Direct (include) condition')], 'optionsKey' => true],
    __('Specify condition type of validation expression. Direct - if(cond), reverse - if(!cond)')
) ?>
<?php if ($model->reg_exp): ?>
<div class="row">
    <div class="col-md-3">
        <label class="pull-right"><?= __('How it work') ?></label>
    </div>
    <div class="col-md-9">
<pre>
if (<?= $model->reg_cond == 0 ? '!' : null ?>preg_match('<?= $model->reg_exp ?>', $input)) {
    // validation passed
} else {
    // validation failed
}
</pre>
    </div>
</div>
<?php endif; ?>

<div class="col-md-9 col-md-offset-3">
    <?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>
    <?= Url::link(['profile/fieldlist'], __('Cancel'), ['class' => 'btn btn-default']) ?>
</div>

<?= $form->finish() ?>
