<?php

/** @var $records object */
/** @var $pagination object */
/** @var $this object */
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Url;

$this->title = __('User list');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    __('User list')
];

?>

<?= $this->render('user/_tabs') ?>

<h1><?= __('User list') ?></h1>
<hr />
<?php
    $items = [];
    foreach ($records as $user) {
        $items[] = [
            1 => ['text' => $user->id],
            2 => ['text' => $user->email],
            3 => ['text' => $user->login],
            4 => ['text' => $user->role->name],
            5 => ['text' => Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY)],
            6 => ['text' => Url::link(['user/update', $user->id], '<i class="fa fa-pencil fa-lg"></i>') .
                Url::link(['user/delete', $user->id], ' <i class="fa fa-trash-o fa-lg"></i>'),
                'html' => true, 'property' => ['class' => 'text-center']],
            'property' => [
                'class' => 'checkbox-row' . ($user->approve_token != '0' ? ' alert-warning' : null)
            ]
        ];
    }
?>

<div class="pull-right">
    <?= Url::link(['user/invite'], __('Send invite'), ['class' => 'btn btn-primary']) ?>
    <?= Url::link(['user/update', '0'], __('Add user'), ['class' => 'btn btn-primary']) ?>
</div>

<?=  Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Email')],
            ['text' => __('Login')],
            ['text' => __('Role')],
            ['text' => __('Register date')],
            ['text' => __('Actions')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ],
    'selectableBox' => [
        'attachOrder' => 1,
        'form' => ['method' => 'GET', 'class' => 'form-horizontal', 'action' => Url::to('user/delete')],
        'selector' => ['type' => 'checkbox', 'name' => 'selected[]', 'class' => 'massSelectId'],
        'buttons' => [
            ['type' => 'submit', 'class' => 'btn btn-danger', 'value' => __('Delete selected')]
        ]
    ]
])?>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>