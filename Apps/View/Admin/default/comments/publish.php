<?php
use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Type\Str;

/** @var Apps\Model\Admin\Comments\FormCommentDelete $model */
/** @var string $type */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Publish comments'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('widget/index') => __('Widgets'),
        Url::to('comments/index') => __('Comments'),
        __('Publish comments and answers')
    ]
]);
$records = $model->getRecord();
?>

<?php $this->start('body') ?>

<?= $this->insert('comments/_tabs') ?>

<h1><?= __('Publish comments and answers') ?></h1>
<?= __('Are you sure to moderate and make public this comments and answers?') ?>

<?php
$table = $this->table(['class' => 'table table-striped'])
    ->head([
        ['text' => '#'],
        ['text' => __('Message')],
        ['text' => __('Author')],
        ['text' => __('Date')]
    ]);
foreach ($records as $item) {
    $table->row([
        ['text' => $item->id],
        ['text' => Str::sub(\App::$Security->strip_tags($item->message), 0, 50)],
        ['text' => Simplify::parseUserLink($item->user_id, $item->guest_name, 'user/update'), 'html' => true],
        ['text' => Date::convertToDatetime($item->created_at, Date::FORMAT_TO_HOUR)]
    ]);
}

?>
<div class="table-responsive"><?= $table->display() ?></div>

<?php $form = $this->form($model) ?>
<?= $form->start() ?>

<?= $form->button()->submit(__('Publish'), ['class' => 'btn btn-warning'])?>

<?= $form->stop() ?>

<?php $this->stop() ?>
