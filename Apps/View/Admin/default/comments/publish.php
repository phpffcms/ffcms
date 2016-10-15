<?php
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var Apps\Model\Admin\Comments\FormCommentDelete $model */
/** @var string $type */
/** @var \Ffcms\Core\Arc\View $this */

$this->title = __('Publish comments');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    Url::to('comments/index') => __('Comments'),
    __('Publish comments and answers')
];

$records = $model->getRecord();

?>

<?= $this->render('comments/_tabs') ?>

<h1><?= __('Publish comments and answers') ?></h1>
<hr />
<?= __('Are you sure to moderate and make public this comments and answers?') ?>

<?php
$items = [];
foreach ($records as $item) {
    $message = Str::sub(\App::$Security->strip_tags($item->message), 0, 50);
    $author = Simplify::parseUserLink($item->user_id, $item->guest_name, 'user/update');

    $items[] = [
        ['text' => $item->id],
        ['text' => $message],
        ['text' => $author, 'html' => true],
        ['text' => Date::convertToDatetime($item->created_at, Date::FORMAT_TO_HOUR)]
    ];
}

?>
<div class="table-responsive">
<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Message')],
            ['text' => __('Author')],
            ['text' => __('Date')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ]
]) ?>
</div>


<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post'])?>
<?= $form->start() ?>

<?= $form->submitButton(__('Publish'), ['class' => 'btn btn-warning'])?>

<?= $form->finish() ?>