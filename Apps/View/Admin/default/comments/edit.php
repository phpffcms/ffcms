<?php 
use Ffcms\Core\Helper\Url;
use Ffcms\Core\Helper\HTML\Form;

/** @var Apps\Model\Admin\Comments\FormCommentUpdate $model */
/** @var string $type */

$this->title = __('Edit comment');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('widget/index') => __('Widgets'),
    Url::to('comments/index') => __('Comments'),
    __('Edit comment or answer')
];

// use wysiwyg for comment editing by className
echo Ffcms\Widgets\Ckeditor\Ckeditor::widget(['targetClass' => 'wysi-comments', 'config' => 'config-small']);

?>

<?= $this->render('comments/_tabs') ?>

<h1><?= __('Edit comment/answer') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post'])?>
<?= $form->start() ?>

<?= $form->field('guestName', 'text', ['class' => 'form-control'], __('Guest name for this comment or answer if defined')) ?>
<?= $form->field('message', 'textarea', ['class' => 'form-control wysi-comments'], __('Comment message text')) ?>

<?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary'])?>

<?= Url::link(['comments/read', $model->getCommentId()], __('Back'), ['class' => 'btn btn-warning'])?>

<?= $form->finish() ?>