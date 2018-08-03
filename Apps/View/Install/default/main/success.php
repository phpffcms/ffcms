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
    <i class="fa fa-check-circle-o fa-4x" style="color: green;"></i>
</p>
<a href="<?= \App::$Alias->scriptUrl ?>" class="btn btn-success btn-block"><?= __('Goto website') ?></a>
<?php $this->stop() ?>