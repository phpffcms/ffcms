<?php

$this->layout('_layouts/default');

/** @var string $msg */
/** @var \Ffcms\Templex\Template\Template $this */

if (\Ffcms\Core\Helper\Type\Str::likeEmpty($msg)) {
    $msg = 'No error information available yet';
}
?>

<?php $this->start('body') ?>

<?= $this->bootstrap()->alert('danger', $msg); ?>

<?= $this->insert('_exceptions/_back') ?>

<?php $this->stop() ?>
