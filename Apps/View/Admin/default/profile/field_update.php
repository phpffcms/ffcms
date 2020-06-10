<?php

use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Profile\FormFieldUpdate $model */
/** @var \Apps\ActiveRecord\ProfileField $record */

$this->layout('_layouts/default', [
    'title' => __('Manage field')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Manage addition field') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Profile list') => ['profile/index'],
    __('Profile fields') => ['profile/fieldlist'],
    __('Manage field')
]]) ?>

<?= $this->insert('profile/_tabs') ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?php
$menu = $this->bootstrap()->nav('ul', ['class' => 'nav-tabs']);
foreach (\App::$Properties->get('languages') as $lang) {
    $menu->menu([
        'text' => Str::upperCase($lang),
        'tab' => function() use ($form, $lang) {
            return $form->fieldset()->text('name.' . $lang, null, __('Define field name, which be displayed for user for current language locale'));
        },
        'tabActive' => $lang === \App::$Request->getLanguage(),
    ]);
}
?>

<div class="nav-border">
    <?= $menu->display() ?>
</div>

<?= $form->fieldset()->select('type', ['options' => ['text', 'link']], __('Select additional field type')) ?>
<?= $form->fieldset()->text('reg_exp', null, __('Set regular expression to validate input data from user for this field. Example: /^[0-9]*$/')) ?>
<?= $form->fieldset()->select(
    'reg_cond',
    ['options' => [
            '0' => __('Reverse (exclude) condition'),
            '1' => __('Direct (include) condition')
    ], 'optionsKey' => true],
    __('Specify condition type of validation expression. Direct - if(cond), reverse - if(!cond)')
) ?>
<?php if ($model->reg_exp): ?>
    <div class="row">
        <div class="col-md-3">
            <label class="float-right"><?= __('How it work') ?></label>
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

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['profile/fieldlist'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop() ?>

<?= $this->stop() ?>