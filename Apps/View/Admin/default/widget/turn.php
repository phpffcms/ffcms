<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Url;
/** @var $widget object */
/** @var $this object */
/** @var $model object */

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    __('Turn on/off')
];
?>

<h1><?= __('Widget turn on/off') ?></h1>
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
            [['text' => __('Name')], ['text' => $widget->getLocaleName()]],
            [['text' => __('System name')], ['text' => $widget->sys_name]],
            [['text' => __('Last update')], ['text' => Date::convertToDatetime($widget->updated_at, DATE::FORMAT_TO_SECONDS)]],
            [['text' => __('Status')], ['text' => ((int)$widget->disabled === 0) ? 'On' : 'Off'], 'property' => ['class' =>  ((int)$widget->disabled === 0) ? 'alert-success' : 'alert-danger']]
        ]
    ]
]); ?>

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '', 'enctype' => 'multipart/form-data']); ?>

<?= $form->start() ?>

<div class="col-md-12"><?= $form->submitButton(__('Switch'), ['class' => 'btn btn-primary']); ?></div>

<?= $form->finish(); ?>