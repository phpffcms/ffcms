<?php

use Ffcms\Templex\Url\Url;

/** @var Apps\Model\Admin\Profile\FormFieldUpdate $model */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Field delete'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('profile/index') => __('Profile list'),
        Url::to('profile/fieldlist') => __('Profile fields'),
        __('Field delete')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('profile/_tabs') ?>
<h1><?= __('Field delete') ?></h1>
<p><?= __('Are you sure to delete this custom field?') ?></p>
<div class="table-responsive">
    <?= $this->table(['class' => 'table'])
        ->row([
            ['text' => $model->getLabel('name')],
            ['text' => $model->name[\App::$Request->getLanguage()]]
        ])->row([
            ['text' => $model->getLabel('type')],
            ['text' => $model->type]
        ])->row([
            ['text' => $model->getLabel('reg_exp')],
            ['text' => ($model->reg_cond == 0 ? '!' : null) . 'preg_match("'.$model->reg_exp.'", $input)']
        ])->display();
    ?>
</div>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->button()->submit(__('Delete'), ['class' => 'btn btn-danger']) ?>
<?= $form->button()->cancel(__('Cancel'), ['link' => ['profile/fieldlist'], 'class' => 'btn btn-secondary']) ?>

<?= $form->stop(false) ?>

<?php $this->stop() ?>
