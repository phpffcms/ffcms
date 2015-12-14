<?php
/** @var $msg string */
if (\Ffcms\Core\Helper\Type\Str::likeEmpty($msg)) {
    $msg = 'Not founded';
}
?>

<p class="alert alert-warning">
    <?= $msg ?>
</p>
<?= $this->render('errors/_back') ?>