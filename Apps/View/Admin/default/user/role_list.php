<?php

use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\Role[]|\Illuminate\Support\Collection $records */

$this->layout('_layouts/default', [
    'title' => __('Role management')
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Role list') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Users') => ['user/index'],
    __('Role management')
]]) ?>

<?= $this->insert('user/_tabs') ?>

<?php
$table = $this->table(['class' => 'table'])
    ->head([
        ['text' => __('Name')],
        ['text' => __('Permissions')],
        ['text' => __('Color')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ], ['class' => 'thead-light']);
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

    $roleColor = '-';
    if ($role->color) {
        $roleColor = '<span class="badge badge-light" style="color: ' . $role->color . '">' . $role->color . '</span>';
    }

    $table->row([
        ['text' => $role->name],
        ['text' => $permissionsLabel, 'html' => true],
        ['text' => $roleColor, 'html' => true],
        ['text' => Url::a(['user/roleupdate', [$role->id]], '<i class="fas fa-pencil-alt fa-lg"></i>', ['html' => true]), 'properties' => ['class' => 'text-center'], 'html' => true]
    ]);
}
?>

<div>
    <?= Url::a(['user/roleupdate'], __('Add role'), ['class' => 'btn btn-primary my-2']) ?>
</div>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?php $this->stop() ?>
