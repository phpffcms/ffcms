<?php use Ffcms\Core\Helper\Url;

if (null === $id) {
    $id = 0;
}

if ($controller !== null): ?>
    <?php if (false != $create): ?>
        <a href="<?= Url::to($controller . '/create') ?>"><i class="fa fa-plus"></i></a>&nbsp;
    <?php endif; if (false != $read): ?>
        <a href="<?= Url::to($controller . '/read', $id) ?>"><i class="fa fa-table"></i></a>&nbsp;
    <?php endif; if (false != $update): ?>
        <a href="<?= Url::to($controller . '/update', $id) ?>"><i class="fa fa-pencil"></i></a>&nbsp;
    <?php endif; if (false != $delete): ?>
        <a href="<?= Url::to($controller . '/delete', $id) ?>"><i class="fa fa-trash-o"></i></a>
    <?php endif; ?>
<?php endif; ?>