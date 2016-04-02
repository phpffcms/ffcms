<?php
/** @var $msg string */
if (\Ffcms\Core\Helper\Type\Str::likeEmpty($msg)) {
    $msg = 'Unexpected error';
}
?>

    <p class="alert alert-info">
        <?= $msg ?>
    </p>
<?= $this->render('errors/_back') ?>