<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Main\EntityUpdate $entityModel */
/** @var \Apps\Model\Admin\Main\FormUpdateDatabase $dbModel */
/** @var \Apps\Model\Admin\Main\FormUpdateDownload $downloadModel */

$this->layout('_layouts/default', [
    'title' => __('Updates'),
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Update manager') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Updates')
]]) ?>

<div class="table-responsive">
    <?= $this->table(['class' => 'table table-striped table-hover'])
        ->row([
            ['text' => __('Scripts version')],
            ['text' => $entityModel->scriptVersion],
            'properties' => ['class' => $entityModel->haveRemoteNew ? 'warning' : null]
        ])
        ->row([
            ['text' => __('Database version')],
            ['text' => $entityModel->dbVersion],
            'properties' => ['class' => !$entityModel->versionsEqual ? 'danger' : null]
        ])
        ->row([
            ['text' => __('Last version')],
            ['text' => $entityModel->lastVersion],
            'properties' => ['class' => $entityModel->haveRemoteNew ? 'success' : null]
        ])->display() ?>
</div>
<?php if (!$entityModel->versionsEqual): ?>
    <p class="alert alert-warning"><?= __('Seems like scripts and database of your website have different versions. You should do update right now or your website can working unstable') ?></p>
    <p><?= __('This updates for database will be applied:') ?></p>
    <?php
    $li = $this->listing('ul');
    foreach ($dbModel->updateQueries as $file) {
        $li->li(['text' => $file]);
    }
    echo $li->display();

    $form = $this->form($dbModel);
    echo $form->start();
    echo $form->button()->submit(__('Update database'), ['class' => 'btn btn-info']);
    echo $form->stop();
    ?>
<?php elseif ($entityModel->haveRemoteNew): ?>
    <p class="alert alert-warning"><?= __('The newest version: <b>%version%</b> with title &laquo;<em>%title%</em>&raquo; is available to update. You can start update right now', [
            'version' => $entityModel->lastVersion,
            'title' => $entityModel->lastInfo['name']
        ]) ?>
    </p>
    <?php
    $form = $this->form($downloadModel);
    echo $form->start();
    echo $form->button()->submit(__('Download update'), ['class' => 'btn btn-primary']);
    echo $form->stop();
    ?>
<?php else: ?>
    <p class="alert alert-success"><?= __('Your system is up to date. No updates is available') ?></p>
<?php endif; ?>
<?php $this->stop() ?>
