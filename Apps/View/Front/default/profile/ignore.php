<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var $model Apps\Model\Front\Profile\FormIgnoreAdd */
/** @var $this object */
/** @var $records Apps\ActiveRecord\Blacklist */
/** @var $pagination object */

$this->title = __('Blacklist');

$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    Url::to('profile/show', \App::$User->identity()->getId()) => __('Profile'),
    __('Blacklist')
];

?>

<?= $this->render('profile/_settingsTab') ?>

<h2><?= __('Add user ignore') ?></h2>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '', 'method' => 'post']) ?>

<?= $form->field('id', 'text', ['class' => 'form-control'], __('Enter id of user who you want to block')) ?>
<?= $form->field('comment', 'text', ['class' => 'form-control'], __('Remark memo about this block')) ?>

<div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Block'), ['class' => 'btn btn-danger']) ?></div>

<?= $form->finish() ?>

<h2><?= __('List of blocked users') ?></h2>
<hr />
<?php if ($records !== null && $records->count() > 0): ?>
    <?php
    $items = [];
    foreach ($records as $row) {
        $userProfile = $row->getUser()->getProfile();
        $userNick = Str::likeEmpty($userProfile->nick) ? __('No name') : \App::$Security->strip_tags($userProfile->nick);
        $items[] = [
            ['text' => Url::link(['profile/show', $row->target_id], $userNick, ['target' => '_blank']), 'html' => true],
            ['text' => $row->comment],
            ['text' => Date::convertToDatetime($row->created_at, Date::FORMAT_TO_DAY)],
            ['text' => Url::link(['profile/unblock', $row->target_id], '<i class="fa fa-times"></i>'), 'html' => true, 'property' => ['class' => 'text-center']]
        ];
    }
    ?>


    <?= Table::display([
        'table' => ['class' => 'table table-bordered'],
        'thead' => [
            'titles' => [
                ['text' => __('User')],
                ['text' => __('Comment')],
                ['text' => __('Add date')],
                ['text' => __('Actions')]
            ]
        ],
        'tbody' => [
            'items' => $items
        ]
    ]); ?>
    <div class="text-center">
        <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
    </div>
<?php else: ?>
<p><?= __('No users in blacklist!') ?></p>
<?php endif ?>