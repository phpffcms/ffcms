<?php

/** @var $this object */

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

$this->title = __('Category list');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('content/index') => __('Contents'),
    __('Categories')
];

?>

<?= $this->render('content/_tabs') ?>

<h1><?= __('Category list') ?></h1>
<hr />
<div class="row">
    <div class="col-md-12">
        <?= Url::link(['content/categoryupdate', 0], '<i class="fa fa-plus"></i> ' . __('Add category'), ['class' => 'btn btn-primary pull-right']) ?>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-line table-striped table-hover">
        <thead>
        <tr>
            <th class="col-md-10"><?= __('Category') ?></th>
            <th class="col-md-2 text-center"><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $categoryArray = ContentCategory::getSortedAll();
        foreach ($categoryArray as $path => $row):
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
                        <div class="col-md-<?= $offset ?> col-xs-<?= $offset+2 ?>" style="padding-top: 8px;border-bottom: 2px solid #8a8a8a"></div>
                        <div class="col-md-<?= $set ?> col-xs-<?= $set-2 ?>">
                            <?= Serialize::getDecodeLocale($row->title) ?>
                            <sup>id: <?= $row->id ?></sup>
                            <span class="label label-info">/<?= $row->path ?></span>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <?= Url::link(['content/categoryupdate', 0, null, ['parent' => $row->id]], '<i class="fa fa-plus"></i>', ['class' => 'label label-default']) ?>
                    <?= Url::link(['content/categoryupdate', $row->id], '<i class="fa fa-cogs"></i>', ['class' => 'label label-default']) ?>
                    <?php if ($row->id > 1): ?>
                        <?= Url::link(['content/categorydelete', $row->id], '<i class="fa fa-trash-o"></i>', ['class' => 'label label-default']) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>