<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Url;

/** @var $model Apps\Model\Admin\Profile\FormFieldUpdate */

$this->title = __('Field delete');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('profile/index') => __('Profile list'),
    Url::to('profile/fieldlist') => __('Profile fields'),
    __('Field delete')
];

?>

<?= $this->show('profile/_tabs') ?>

<h1><?= __('Field delete') ?></h1>
<hr />
<p><?= __('Are you sure to delete this custom field?') ?></p>
<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => __('Param')],
            ['text' => __('Value')]
        ]
    ],
    'tbody' => [
        'items' => [
            [['text' => $model->getLabel('name')], ['text' => $model->name[\App::$Request->getLanguage()]]],
            [['text' => $model->getLabel('type')], ['text' => $model->type]],
            [['text' => $model->getLabel('reg_exp')], ['text' => ($model->reg_cond == 0 ? '!' : null) . 'preg_match("'.$model->reg_exp.'", $input)']],
        ]
    ]
]);
?>
<?php $form = new Form($model, ['class' => 'form-horizontal', 'action' => '', 'method' => 'post']) ?>

<div class="col-md-12"><?= $form->submitButton(__('Delete'), ['class' => 'btn btn-danger']) ?></div>

<?= $form->finish(false) ?>