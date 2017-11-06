<?php

/** @var Apps\Model\Admin\User\FormUserDelete $model */
/** @var \Ffcms\Core\Arch\View $this */

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Url;

$this->title = __('Delete users');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('user/index') => __('User list'),
    __('Delete users')
];

?>

<?= $this->render('user/_tabs') ?>

<h1><?= __('Delete users') ?></h1>
<hr />
<p><?= __('Are you sure to delete this users?') ?></p>
<?php
$items = [];
foreach ($model->users as $user) {
    /** @var \Apps\ActiveRecord\User $user */
    $nickname = ($user->profile === null ? 'unknown' : $user->profile->getNickname());
    $items[] = [
        ['text' => $user->getParam('id')],
        ['text' => $user->getParam('email')],
        ['text' => $user->getParam('login')],
        ['text' => $nickname],
        ['text' => Date::convertToDatetime($user->created_at, Date::FORMAT_TO_HOUR)]
    ];
}
?>

<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Email')],
            ['text' => __('Login')],
            ['text' => __('Nickname')],
            ['text' => __('Register date')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ]
]) ?>


<?php
    $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '']);
    echo $form->start();
    echo $form->submitButton(__('Delete'), ['class' => 'btn btn-danger']) . "&nbsp;";
    echo Url::link(['user/index'], __('Cancel'), ['class' => 'btn btn-default']);
    echo $form->finish();
?>