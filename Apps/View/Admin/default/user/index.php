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

<?= $this->show('user/_tabs') ?>

<h1><?= __('User list') ?></h1>
<hr />
<?php
    $items = [];
    foreach ($records as $user) {
        $items[] = [
            ['text' => $user->id . ($user->approve_token != '0' ? ' <strong class="text-danger">*</strong>' : null), 'html' => true],
            ['text' => $user->email],
            ['text' => $user->login],
            ['text' => $user->getRole()->name],
            ['text' => Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY)],
            ['text' => Url::link(['user/update', $user->id], '<i class="fa fa-pencil fa-lg"></i>') .
                Url::link(['user/delete', $user->id], ' <i class="fa fa-trash-o fa-lg"></i>'),
                'html' => true, 'property' => ['class' => 'text-center']]
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
            ['text' => 'id'],
            ['text' => __('Email')],
            ['text' => __('Login')],
            ['text' => __('Role')],
            ['text' => __('Register date')],
            ['text' => __('Actions')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ]
])?>

<div>
    <strong class="text-danger">*</strong> - <?= __('User is not approved via email') ?>
</div>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>