<?php
/** @var $msg string */
if (\Ffcms\Core\Helper\Type\Str::likeEmpty($msg)) {
    $msg = 'Code failure';
}
?>

    <p class="alert alert-danger">
        <?= $msg ?>
    </p>