<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\User[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var string $search */

$this->layout('_layouts/default', [
    'title' => __('User list')
]);
?>

<?php $this->start('body') ?>
<h1><?= __('User list') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('User list')
]]) ?>


<?= $this->insert('user/_tabs') ?>

<div class="row pt-3 pb-1">
    <div class="col-md-8">
        <?= Url::a(['user/update'], __('Add user'), ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="col-md-4">
        <form accept-charset="UTF-8" method="get">
            <div class="input-group">
                <input type="text" name="search" id="search" value="<?= $this->e($search) ?>" placeholder="email ..." class="form-control mr-sm-1">
                <span class="input-group-btn">
                    <input type="submit" name="dosearch" value="<?= __('Search') ?>" class="btn btn-primary">
                </span>
            </div>
        </form>
    </div>
</div>

<?php if ($records->count() < 1) {
    echo $this->bootstrap()->alert('warning', __('No users found'));
    $this->stop();
    return;
} ?>

<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Email')],
        ['text' => __('Role')],
        ['text' => __('Register date')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ], ['class' => 'thead-dark']);

foreach ($records as $user) {
    $roleHtml = $user->role->color ? '<span class="badge badge-light" style="color: ' . $user->role->color . '">' . $user->role->name . '</span>' : $user->role->name;

    $btngrp = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm'], 4)
        ->add('<i class="fas fa-pencil-alt"></i>', ['user/update', [$user->id]], ['class' => 'btn btn-primary', 'html' => true])
        ->add('<i class="fas fa-user-slash"></i>', ['main/banupdate', [], ['user' => $user->id]], ['class' => 'btn btn-warning', 'html' => true])
        ->add('<i class="fas fa-eraser"></i>', ['user/clear', [$user->id]], ['class' => 'btn btn-light', 'html' => true])
        ->add('<i class="fas fa-trash-alt"></i>', ['user/delete', [$user->id]], ['class' => 'btn btn-danger', 'html' => true]);

    // user not approved - show approve button
    if ($user->approve_token) {
        $btngrp->add('<i class="fas fa-check"></i>', ['user/approve', [$user->id]], ['class' => 'btn btn-success', 'html' => true]);
    }

    $table->row([
        ['text' => $user->id],
        ['text' => $user->email],
        ['text' => $roleHtml, 'html' => true],
        ['text' => Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY)],
        ['text' => $btngrp->display(),
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