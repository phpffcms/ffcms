<?php
/** @var $msg string */
if (\Ffcms\Core\Helper\Type\Str::likeEmpty($msg)) {
    $msg = 'Access forbidden';
}
?>

    <p class="alert alert-danger">
        <?= $msg ?>
    </p>
<?= $this->render('errors/_back') ?>