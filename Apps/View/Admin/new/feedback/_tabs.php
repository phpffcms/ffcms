<?php
/** @var \Ffcms\Templex\Template\Template $this */
?>

<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs nav-fill'])
    ->menu(['text' => __('Feedback list'), 'link' => ['feedback/index']])
    ->menu(['text' => __('Settings'), 'link' => ['feedback/settings']])
    ->display(); ?>