<?php

use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\Role[]|\Illuminate\Support\Collection $records */

$this->layout('_layouts/default', [
    'title' => __('Role management'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        __('Role management')
    ]
]);
?>

<?php $this->start('body') ?>
<?= $this->insert('user/_tabs') ?>

<h1><?= __('Role list') ?></h1>

<?php
$table = $this->table(['class' => 'table'])
    ->head([
        ['text' => __('Name')],
        ['text' => __('Permissions')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ]);
?>

<?php
foreach($records as $role) {
    $permissions = explode(';', $role->permissions);
    $permissionsLabel = null;
    foreach ($permissions as $perm) {
        $labelMark = null;
        if (Str::startsWith('Admin/', $perm)) {
            $labelMark = 'badge-primary';
        } elseif ($perm === 'global/all') {
            $labelMark = 'badge-danger';
        } else {
            $labelMark = 'badge-secondary';
        }
        $permissionsLabel .= '<span class="badge ' . $labelMark . '">' . $perm . '</span> ';
    }
    $table->row([
        ['text' => $role->name],
        ['text' => $permissionsLabel, 'html' => true],
        ['text' => Url::a(['user/roleupdate', [$role->id]], '<i class="fa fa-pencil fa-lg"></i>', ['html' => true]), 'properties' => ['class' => 'text-center'], 'html' => true]
    ]);
}
?>

<div>
    <?= Url::a(['user/roleupdate'], __('Add role'), ['class' => 'btn btn-primary']) ?>
</div>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?php $this->stop() ?>
