<?php

/** @var $records object */
/** @var $pagination object */
/** @var $this object */
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Helper\Url;

$this->title = __('Profile list');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    __('Profile')
];

?>

<?= $this->show('profile/_tabs') ?>

<h1><?= __('Profile list') ?></h1>
<hr />
<?php
$items = [];
foreach ($records as $profile) {
    $items[] = [
        ['text' => $profile->id],
        ['text' => $profile->User()->login . '/' . $profile->User()->email],
        ['text' => $profile->nick],
        ['text' => String::startsWith('0000-', $profile->birthday) ? __('None') : Date::convertToDatetime($profile->birthday)],
        ['text' => ($profile->rating > 0 ? '+' : null) . $profile->rating],
        ['text' => \App::$View->show('macro/crud_actions', ['controller' => 'profile', 'update' => true, 'id' => $profile->id, 'delete' => true]),
            'html' => true, 'property' => ['class' => 'text-center']]
    ];
}
?>

<?=  Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => 'id'],
            ['text' => 'login/email'],
            ['text' => __('Nickname')],
            ['text' => __('Birthday')],
            ['text' => __('Rating')],
            ['text' => __('Actions')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ]
])?>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>