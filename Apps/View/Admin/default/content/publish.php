<?php

/** @var $records object */
/** @var $this object */
/** @var $model \Apps\Model\Admin\Content\FormContentPublish */

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Url;

$this->title = __('Content publish');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/index') => __('Contents'),
    __('Content publish')
];

?>

<?= $this->render('content/_tabs') ?>

<h1><?= __('Content publish') ?></h1>
<hr />
<p><?= __('Are you sure to make this item public?') ?></p>
<?php
$items = [];
foreach ($records as $record) {
    /** @var $record \Apps\ActiveRecord\Content */
    $items[] = [
        ['text' => $record->id],
        ['text' => $record->getLocaled('title')],
        ['text' => Simplify::parseUserLink($record->author_id), 'html' => true],
        ['text' => Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR)]
    ];
}
?>

<div class="table-responsive">
    <?= Table::display([
        'table' => ['class' => 'table table-bordered'],
        'thead' => [
            'titles' => [
                ['text' => '#'],
                ['text' => __('Title')],
                ['text' => __('Author')],
                ['text' => __('Date')]
            ]
        ],
        'tbody' => [
            'items' => $items
        ]
    ]) ?>
</div>

<?php $form = new Form($model, ['class' => 'form-horizontal']); ?>
<?= $form->start() ?>

<?= $form->submitButton(__('Publish'), ['class' => 'btn btn-warning']) ?>

<?= $form->finish() ?>
