<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\User[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('User list'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        __('User list')
    ]
]);
?>

<?php $this->start('body') ?>
<?= $this->insert('user/_tabs') ?>
<h1><?= __('User list') ?></h1>
<div>
    <?= Url::a(['user/update'], __('Add user'), ['class' => 'btn btn-primary']) ?>
</div>
<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Email')],
        ['text' => __('Login')],
        ['text' => __('Role')],
        ['text' => __('Register date')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ]);


foreach ($records as $user) {
    $table->row([
        ['text' => $user->id],
        ['text' => $user->email],
        ['text' => $user->login],
        ['text' => $user->role->name],
        ['text' => Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY)],
        ['text' => Url::a(['user/update', [$user->id]], '<i class="fa fa-pencil fa-lg"></i>', ['html' => true]) .
            Url::a(['user/delete', [$user->id]], ' <i class="fa fa-trash-o fa-lg"></i>', ['html' => true]),
            'properties' => ['class' => 'text-center'], 'html' => true],
        'properties' => ['class' => 'checkbox-row' . ($user->approve_token !== null ? ' bg-warning' : null)]

    ]);
}
$table->selectize(0, 'selected');
?>

<div class="table-responsive">
    <?= $table->display(); ?>
</div>

<?= $this->javascript()->submitSelectizeTable('input[name="selected[]"]', 'selected', __('Delete selected'), ['user/delete'], ['class' => 'btn btn-danger']) ?>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<?php $this->stop() ?>