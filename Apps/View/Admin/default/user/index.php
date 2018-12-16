<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\User[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var string $search */

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
<div class="row">
    <div class="col-md-8">
        <?= Url::a(['user/update'], __('Add user'), ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="col-md-4">
        <form accept-charset="UTF-8" method="get">
            <div class="input-group">
                <input type="text" name="search" id="search" value="<?= $this->e($search) ?>" placeholder="login, email ..." class="form-control mr-sm-1">
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
        ['text' => __('Login')],
        ['text' => __('Role')],
        ['text' => __('Register date')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ]);

foreach ($records as $user) {
    $actionMenu = '<div class="btn-group btn-group-sm">';
    $actionMenu .= Url::a(['user/update', [$user->id]], '<i class="fa fa-pencil"></i>', ['html' => true, 'class' => 'btn btn-sm btn-primary']);
    $actionMenu .= Url::a(['user/delete', [$user->id]], ' <i class="fa fa-trash-o"></i>', ['html' => true, 'class' => 'btn btn-sm btn-danger']);
    $actionMenu .= '</div>';

    $roleHtml = $user->role->color ? '<span class="badge badge-light" style="color: ' . $user->role->color . '">' . $user->role->name . '</span>' : $user->role->name;

    $table->row([
        ['text' => $user->id],
        ['text' => $user->email],
        ['text' => $user->login],
        ['text' => $roleHtml, 'html' => true],
        ['text' => Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY)],
        ['text' => $actionMenu,
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