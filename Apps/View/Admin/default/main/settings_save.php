<?php

use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Settings are saved'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        Url::to('main/settings') => __('Settings')
    ]
])
?>
<?php $this->start('body') ?>
<h1><?= __('Congratulations!') ?></h1>
<p><?= __('Settings are successful saved! Wait 5 second to update configurations') ?></p>
<?= Url::a(['main/settings'], __('Reload'), ['class' => 'btn btn-primary']); ?>
<?php $this->stop(); ?>


<?php $this->push('javascript') ?>
<script>
    setTimeout(function () {
        window.location.replace("<?= Url::to('main/settings') ?>");
    }, 5000);
</script>
<?php $this->stop() ?>
