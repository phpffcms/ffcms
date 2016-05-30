<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var $model Apps\Model\Admin\Search\FormSettings */

$this->title = __('Search settings');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('search/index') => __('Search'),
    __('Settings')
];

?>

<h1><?= __('Search settings') ?></h1>
<hr />
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '', 'method' => 'post']) ?>

<?= $form->start() ?>

<?= $form->field('minLength', 'text', ['class' => 'form-control'], __('Set minimum user search query length. We are strongly recommend set this value more then 2.')) ?>
<?= $form->field('itemPerApp', 'text', ['class' => 'form-control'], __('How many founded items would be displayd for each search instance?')) ?>

<div class="col-md-offset-3 col-md-9"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>