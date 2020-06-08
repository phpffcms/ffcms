<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\Model\Admin\Main\FormBanUpdate $model */

$this->layout('_layouts/default', [
    'title' => __('Main')
]);

?>

<?php $this->push('css') ?>
<!-- jquery ui plugin -->
<link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/jquery-ui-dist/jquery-ui.min.css" />
<?php $this->stop() ?>

<?php $this->start('body') ?>

<h1><?= __('Ban update') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Ban') => ['main/ban'],
    __('Ban update')
]]) ?>

<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs nav-fill'])
    ->menu(['text' => __('Spam'), 'link' => ['main/spam']])
    ->menu(['text' => __('Ban'), 'link' => ['main/ban']])
    ->display(); ?>


<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('ip', ['class' => 'form-control'], __('Set IPv4 address to block')) ?>
<?= $form->fieldset()->text('userId', ['class' => 'form-control'], __('Set user id to block')) ?>

<?= $form->fieldset()->boolean('write', null, __('Block website write?')) ?>
<?= $form->fieldset()->boolean('read', null, __('Block website read? Attention: user will not be permitted to see website content!')) ?>

<?= $form->fieldset()->text('expire', ['class' => 'form-control datepick'], __('Set block expires date or left empty to permaban')) ?>
<?= $form->fieldset()->boolean('perma') ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>
<?= $form->button()->cancel(__('Cancel'), ['class' => 'btn btn-secondary', 'link' => ['main/ban']]) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/jquery-ui-dist/jquery-ui.min.js"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/jquery-datepicker/jquery-datepicker.js"></script>
<script>
$(function(){
    $('.datepick').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $('#FormBanUpdate-perma').change(function(){
        if (this.checked) {
            $('#FormBanUpdate-expire').val("");
        }
    });
    $('#FormBanUpdate-expire').change(function(){
        $('#FormBanUpdate-perma').prop('checked', false);
    });
});
</script>
<?php $this->stop() ?>