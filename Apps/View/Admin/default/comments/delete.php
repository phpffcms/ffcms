<?php 
use Ffcms\Core\Helper\Url;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Simplify;

/** @var Apps\Model\Admin\Comments\FormCommentDelete $model */
/** @var string $type */

$this->title = __('Delete comments');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    Url::to('comments/index') => __('Comments'),
    __('Delete comments and answers')
];

$records = $model->getRecord();

?>

<?= $this->render('comments/_tabs') ?>

<h1><?= __('Delete comments and answers') ?></h1>
<hr />
<?= __('Are you sure to delete this comments or answers?') ?>

<?php 
$items = [];
foreach ($records as $item) {
    $message = Str::sub(\App::$Security->strip_tags($item->message), 0, 50);
    $author = Simplify::parseUserNick($item->user_id, $item->guest_name);
    if ((int)$item->user_id > 0) {
        $author = Url::link(['user/update', (int)$item->user_id], $author);
    }
    
    
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

<?= $form->submitButton(__('Delete'), ['class' => 'btn btn-danger'])?>

<?= $form->finish() ?>