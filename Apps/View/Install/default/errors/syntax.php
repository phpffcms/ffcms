<?php
/** @var $msg string */
if (\Ffcms\Core\Helper\Type\Str::likeEmpty($msg)) {
    $msg = 'Code failure';
}
?>

    <p class="alert alert-primary">
        <?= $msg ?>
    </p>
<?= $this->render('errors/_back') ?>