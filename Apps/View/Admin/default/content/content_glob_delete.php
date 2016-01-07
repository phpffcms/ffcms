<?php

/** @var $this object */
/** @var $model Apps\Model\Admin\Content\FormContentGlobDelete */

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Url;

$this->title = __('Content global delete');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/index') => __('Contents'),
    __('Content delete')
];

?>

<?= $this->render('content/_tabs') ?>
<h1><?= $this->title ?></h1>
<hr />
<p><?= __('Are you sure to delete all this content items?'); ?></p>
<?php $form = new Form($model, ['class' => 'form-horizontal']) ?>

<?= $form->start() ?>

<?php
$items = [];
foreach ($model->data as $item) {
    $items[] = [
        ['type' => 'text', 'text' => $item['id']],
        ['type' => 'text', 'text' => $item['title']],
        ['type' => 'text', 'text' => $item['date']]
    ];
}
?>

<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Title')],
            ['text' => __('Date')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ]
]) ?>

<?= $form->submitButton(__('Delete all'), ['class' => 'btn btn-danger']) ?>
<?= $form->finish() ?>