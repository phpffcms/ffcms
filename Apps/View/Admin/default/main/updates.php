<?php
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var \Apps\Model\Admin\Main\EntityUpdate $entityModel */
/** @var \Apps\Model\Admin\Main\FormUpdateDatabase $dbModel */
/** @var \Apps\Model\Admin\Main\FormUpdateDownload $downloadModel */

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Updates')
];
$this->title = __('Updates');
?>
<h1><?= __('Update manager') ?></h1>
<hr />
<div class="table-responsive">
<?= \Ffcms\Core\Helper\HTML\Table::display([
    'table' => ['class' => 'table table-striped table-hover'],
    'tbody' => [
        'items' => [
            [['text' => __('Scripts version')], ['text' => $entityModel->scriptVersion], 'property' => ['class' => $entityModel->haveRemoteNew ? 'warning' : null]],
            [['text' => __('Database version')], ['text' => $entityModel->dbVersion], 'property' => ['class' => !$entityModel->versionsEqual ? 'danger' : null]],
            [['text' => __('Last version')], ['text' => $entityModel->lastVersion], 'property' => ['class' => $entityModel->haveRemoteNew ? 'success' : null]]
        ]
    ]
]); ?>
</div>
<?php if (!$entityModel->versionsEqual): ?>
    <p><?= __('Seems like scripts and database of your website have different versions. You should do update right now or your website can working unstable') ?></p>
    <p><?= __('This updates for database will be applied:') ?></p>
    <?php
    $items = [];
        foreach ($dbModel->updateQueries as $file) {
            $items[] = ['text' => $file];
        }
        echo \Ffcms\Core\Helper\HTML\Listing::display([
            'type' => 'ul',
            'items' => $items
        ]);
    $form = new Form($dbModel, ['class' => 'form-horizontal', 'action' => '']);
    echo $form->start();
    echo $form->submitButton(__('Update database'), ['class' => 'btn btn-info']);
    echo $form->finish();
    ?>
<?php elseif ($entityModel->haveRemoteNew): ?>
    <p><?= __('The newest version: <b>%version%</b> with title &laquo;<em>%title%</em>&raquo; is available to update. You can start update right now', [
            'version' => $entityModel->lastVersion,
            'title' => $entityModel->lastInfo['name']
        ]) ?>
    </p>
    <?php
    $form = new Form($downloadModel, ['class' => 'form-horizontal', 'action' => '']);
    echo $form->start();
    echo $form->submitButton(__('Download update'), ['class' => 'btn btn-primary']);
    echo $form->finish();
    ?>
<?php else: ?>
    <p><?= __('Your system is up to date. No updates is available') ?></p>
<?php endif; ?>
