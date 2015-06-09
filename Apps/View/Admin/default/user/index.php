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

<h1>User list</h1>
<hr />
<?php
    $items = [];
    foreach ($records as $user) {
        $items[] = [
            ['text' => $user->id],
            ['text' => $user->email],
            ['text' => $user->login],
            ['text' => $user->nick],
            ['text' => $user->getRole()->name],
            ['text' => Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY)],
            ['text' => \App::$View->show('macro/crud_actions', ['controller' => 'user', 'update' => true, 'id' => $user->id, 'delete' => true]),
                'html' => true, 'property' => ['class' => 'text-center']]
        ];
    }
?>


<?=  Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => 'id'],
            ['text' => 'Email'],
            ['text' => 'Login'],
            ['text' => 'Nickname'],
            ['text' => 'Role'],
            ['text' => 'Register date'],
            ['text' => 'Actions']
        ],
        'property' => ['id' => 'thead_main']
    ],
    'tbody' => [
        'property' => ['id' => 'tbodym'],
        'items' => $items
    ]
])?>

<div class="text-center">
    <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
</div>