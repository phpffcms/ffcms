<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var array $categories */

use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Category list'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('application/index') => __('Applications'),
        Url::to('content/index') => __('Contents'),
        __('Categories')
    ]
]);

?>

<?php $this->start('body') ?>

<?= $this->insert('content/_tabs') ?>

<h1><?= __('Category list') ?></h1>
<div class="row">
    <div class="col-md-12">
        <?= Url::a(['content/categoryupdate'], '<i class="fa fa-plus"></i> ' . __('Add category'), ['class' => 'btn btn-primary', 'html' => true]) ?>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th class="col-md-10"><?= __('Category') ?></th>
            <th class="col-md-2 text-center"><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($categories as $path => $row):
            $offset = 2;
            $nesting = 0;
            if ($row->path === '') {
                --$offset;
            } else {
                $nesting = Str::entryCount($row->path, '/');
                $offset += $nesting;
            }
            if ($offset > 8) {
                $offset = 8;
            }
            $set = 12 - $offset;
            ?>
            <tr>
                <td>
                    <div class="row">
                        <div class="d-none d-md-block col-md-<?= $offset ?> col-xs-<?= $offset+2 ?> " style="border-bottom: 2px solid #8a8a8a;height: 1px;padding-top: 10px;"></div>
                        <div class="col-md-<?= $set ?> col-xs-<?= $set-2 ?>">
                            <?= $row->getLocaled('title') ?>
                            <sup>id: <?= $row->id ?></sup>
                            <span class="badge badge-secondary">/<?= $row->path ?></span>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <?php $btn = $this->bootstrap()->btngroup(['class' => 'btn-group btn-group-sm', 'role' => 'group'])
                        ->add('<i class="fa fa-plus"></i>', ['content/categoryupdate', null, ['parent' => $row->id]], ['class' => 'btn btn-success', 'data-toggle' => 'tooltip', 'title' => __('Add subcategory'), 'html' => true])
                        ->add('<i class="fa fa-cog"></i>', ['content/categoryupdate', [$row->id]], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'title' => __('Category configurations'), 'html' => true]);
                    if ($row->id > 1) {
                        $btn = $btn->add('<i class="fa fa-trash-o"></i>', ['content/categorydelete', [$row->id]], ['class' => 'btn btn-danger', 'data-toggle' => 'tooltip', 'title' => __('Delete category'), 'html' => true]);
                    }
                    echo $btn->display();
                    ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php $this->stop() ?>