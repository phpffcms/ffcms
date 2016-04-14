<?php

use Ffcms\Core\Helper\Url;
use Ffcms\Core\Helper\HTML\Form;

/** @var Apps\Model\Admin\Contenttag\FormSettings $model */
/** @var Ffcms\Core\Arch\View $this */

$this->title = __('Content tags');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('contenttag/index') => __('Content tags'),
    __('Settings')
];

?>

<h1><?= __('Content tags') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal']); ?>
<?= $form->start() ?>

<?= $form->field('count', 'text', ['class' => 'form-control'], __('Set count of displayed tags in widget block'))?>
<?= $form->field('cache', 'text', ['class' => 'form-control'], __('Set default widget caching time. Set 0 to disable cache')) ?>

<?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->finish() ?>