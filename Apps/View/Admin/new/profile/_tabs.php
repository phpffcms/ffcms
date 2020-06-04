<?php
/** @var \Ffcms\Templex\Template\Template $this */
?>


<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs nav-fill'])
    ->menu(['text' => __('Profile list'), 'link' => ['profile/index']])
    ->menu(['text' => __('Profile fields'), 'link' => ['profile/fieldlist']])
    ->menu(['text' => __('Settings'), 'link' => ['profile/settings']])
    ->display()
?>