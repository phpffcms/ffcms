<?php

/** @var $records object */
/** @var $this object */
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Helper\Url;

$this->title = __('Group management');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    __('Group management')
];

?>

<?= $this->show('user/_tabs') ?>

<h1><?= __('Group list') ?></h1>
<hr />

<?php
$items = [];
foreach($records as $role) {
    $permissions = explode(';', $role->permissions);
    $permissionsLabel = null;
    foreach ($permissions as $perm) {
        $labelMark = null;
        if (String::startsWith('admin/', $perm)) {
            $labelMark = 'label-warning';
        } elseif ($perm === 'global/all') {
            $labelMark = 'label-danger';
        } else {
            $labelMark = 'label-default';
        }
        $permissionsLabel .= '<span class="label ' . $labelMark . '">' . $perm . '</span> ';
    }
    $items[] = [
        ['text' => $role->id],
        ['text' => $role->name],
        ['text' => $permissionsLabel, 'html' => true],
        ['text' => Url::link(['user/groupupdate', $role->id], '<i class="fa fa-pencil"></i>'), 'property' => ['class' => 'text-center'], 'html' => true]
    ];
}

?>

<div class="pull-right"><?= Url::link(['user/groupupdate', '0'], __('Add group'), ['class' => 'btn btn-primary']) ?></div>

<?=  Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => 'id'],
            ['text' => __('Name')],
            ['text' => __('Permissions')],
            ['text' => __('Actions')]
        ],
        'property' => ['id' => 'thead_main']
    ],
    'tbody' => [
        'items' => $items
    ]
])?>