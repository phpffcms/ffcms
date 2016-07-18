<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Url;
/** @var $app object */
/** @var $this object */
/** @var $model object */

$this->title = __('Turn on/off');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    __('Turn on/off')
];
?>

<h1><?= __('Application turn on/off') ?></h1>
<hr />

<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => __('Param')],
            ['text' => __('Value')]
        ]
    ],
    'tbody' => [
        'items' => [
            [['text' => __('Name')], ['text' => $app->getLocaleName()]],
            [['text' => __('System name')], ['text' => $app->sys_name]],
            [['text' => __('Last update')], ['text' => Date::convertToDatetime($app->updated_at, DATE::FORMAT_TO_SECONDS)]],
            [['text' => __('Status')], ['text' => ((int)$app->disabled === 0) ? 'On' : 'Off'], 'property' => ['class' =>  ((int)$app->disabled === 0) ? 'alert-success' : 'alert-danger']]
        ]
    ]
]); ?>

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '', 'enctype' => 'multipart/form-data']); ?>

<?= $form->start() ?>

<div class="col-md-12">
    <?= $form->submitButton(__('Switch'), ['class' => 'btn btn-primary']); ?>
    <?= Url::link(['application/index'], __('Cancel'), ['class' => 'btn btn-default']); ?>
</div>

<?= $form->finish(); ?>