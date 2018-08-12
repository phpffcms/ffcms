<?php

use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;
use Ffcms\Core\Helper\Date;

/** @var \Apps\ActiveRecord\Profile[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Profile list'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        __('Profile')
    ]
]);
?>

<?php $this->start('body') ?>

<?= $this->insert('profile/_tabs') ?>

<h1><?= __('Profile list') ?></h1>
<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => 'id'],
        ['text' => 'login'],
        ['text' => 'email'],
        ['text' => __('Nickname')],
        ['text' => __('Birthday')],
        ['text' => __('Rating')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ]);

foreach ($records as $profile) {
    $table->row([
        ['text' => $profile->id],
        ['text' => $profile->user->login],
        ['text' => $profile->user->email],
        ['text' => $profile->nick],
        ['text' => Str::startsWith('0000-', $profile->birthday) ? __('None') : Date::convertToDatetime($profile->birthday)],
        ['text' => ($profile->rating > 0 ? '+' : null) . $profile->rating],
        ['text' => Url::a(['profile/update', [$profile->id]], '<i class="fa fa-pencil fa-lg"></i> ', ['html' => true]) .
            Url::a(['user/delete', [$profile->user->id]], '<i class="fa fa-trash-o fa-lg"></i>', ['html' => true]),
            'html' => true, 'properties' => ['class' => 'text-center']]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= $this->bootstrap()->pagination($pagination['url'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<?php $this->stop() ?>