<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Application\FormUpdate */
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->title = __('Update app');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    __('Update')
];
?>
<h1><?= __('Update app'); ?></h1>
<hr />
<?= \Ffcms\Core\Helper\HTML\Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => __('App name')],
            ['text' => __('Script version')],
            ['text' => __('DB version')],
            ['text' => __('Last changes')]
        ]
    ],
    'tbody' => [
        'items' => [
            [['text' => $model->name],
            ['text' => $model->scriptVersion],
            ['text' => $model->dbVersion],
            ['text' => $model->date]]
        ]
    ]
]); ?>

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']) ?>
<?= $form->start() ?>

<?= $form->submitButton(__('Try update'), ['class' => 'btn btn-primary']) ?>

<?= $form->finish(false) ?>
