<?php

$this->layout('_layouts/default', [
    'title' => '404 - Not found'
]);

/** @var string $msg */
/** @var \Ffcms\Templex\Template\Template $this */

if (\Ffcms\Core\Helper\Type\Str::likeEmpty($msg)) {
    $msg = 'No error information available yet';
}
?>

<?php $this->start('body') ?>

<?= $this->bootstrap()->alert('info', $msg); ?>

<?= $this->insert('_core/exceptions/_back') ?>

<?php $this->stop(); ?>
