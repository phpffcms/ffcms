<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\Spam[] $records */
/** @var array $stats */
/** @var boolean $tokenActive */
/** @var array $yandexCfg */
/** @var array|null $visits */
/** @var array|null $sources */

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Main')
]);

?>

<?php $this->start('body') ?>

<h1><?= __('Spam threats') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Spam')
]]) ?>

<?php
$table = $this->table(['class' => 'table table-striped datatable'])
    ->head([
        ['text' => '#'],
        ['text' => 'IP'],
        ['text' => 'User id'],
        ['text' => 'Count']
    ], ['class' => 'thead-dark']);

foreach ($records as $record) {
    $table->row([
        ['text' => $record->id],
        ['text' => $record->ipv4],
        ['text' => $record->user_id ?? '?'],
        ['text' => $record->counter]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display()
?>


<?php $this->stop() ?>