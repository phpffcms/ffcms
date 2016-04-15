<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var \Apps\Model\Admin\Newcomment\FormSettings $model */
/** @var \Ffcms\Core\Arch\View $this */

$this->title = __('New comments');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('newcomment/index') => __('New comments'),
    __('Settings')
];

?>

<h1><?= __('New comments') ?></h1>
<hr />

<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']) ?>

<?= $form->start() ?>

<?= $form->field('snippet', 'text', ['class' => 'form-control'], __('Maximum length of comment text displayed in this widget'))?>
<?= $form->field('count', 'text', ['class' => 'form-control'], __('How many comments would be displayed in block?'))?>
<?= $form->field('cache', 'text', ['class' => 'form-control'], __('Widget default cache time in seconds. Set 0 to disable caching'))?>

<?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->finish() ?>
