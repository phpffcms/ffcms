<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var $model \Apps\Model\Install\Main\EntityCheck */

$this->layout('_layouts/default', [
    'title' => 'Installation done!'
])
?>

<?php $this->start('body') ?>
<h1><?= __('Congratulations!') ?></h1>
<hr />
<p class="text-center">
    <i class="far fa-check-circle fa-4x" style="color: green;"></i>
</p>
<a href="<?= \App::$Alias->scriptUrl ?>" class="btn btn-success w-100"><?= __('Goto website') ?></a>
<?php $this->stop() ?>