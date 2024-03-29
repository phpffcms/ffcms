<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var Apps\ActiveRecord\ProfileField $records */

$this->layout('_layouts/default', [
    'title' => __('Profile fields'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('profile/index') => __('Profile list'),
        __('Profile fields')
    ]
]);
?>

<?php $this->start('body') ?>

<h1><?= __('Additional profile fields') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Applications') => ['application/index'],
    __('Profile list') => ['profile/index'],
    __('Profile fields')
]]) ?>

<?= $this->insert('profile/_tabs') ?>

<div class="row">
    <div class="col-md-12">
        <?= Url::a(['profile/fieldupdate'], __('Add field'), ['class' => 'btn btn-primary my-2', 'html' => true]) ?>
    </div>
</div>

<?php if ($records->count() < 1) {
    echo $this->bootstrap()->alert('warning', __('No additional fields is add yet!'));
    $this->stop();
    return;
} ?>

<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Title')],
        ['text' => __('Type')],
        ['text' => __('Rule')],
        ['text' => __('Actions'), 'properties' => ['class' => 'text-center']]
    ], ['class' => 'thead-light']);

foreach ($records as $row) {
    $labelClass = 'badge ';
    $labelClass .= ($row->type === 'link' ? 'badge-primary' : 'badge-secondary');

    $table->row([
        ['text' => $row->id],
        ['text' => $row->getLocaled('name')],
        ['text' => '<span class="' . $labelClass . '">' . $row->type . '</span>', 'html' => true],
        ['text' => '<code>' . ($row->reg_cond == 0 ? '!' : null) . 'preg_match("' . $row->reg_exp . '", input)' . '</code>', 'html' => true],
        ['text' => $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm'])
            ->add('<i class="fas fa-pencil-alt"></i>', ['profile/fieldupdate', [$row->id]], ['class' => 'btn btn-primary', 'html' => true])
            ->add('<i class="fas fa-trash-alt"></i>', ['profile/fielddelete', [$row->id]], ['class' => 'btn btn-danger', 'html' => true])
            ->display(),
            'html' => true,
            'properties' => ['class' => 'text-center']
        ]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display(); ?>
</div>

<?php $this->stop() ?>