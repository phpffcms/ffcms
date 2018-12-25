<?php
/** @var \Ffcms\Templex\Template\Template $this */
?>

<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs nav-fill'])
    ->menu(['text' => __('Comments'), 'link' => ['comments/index']])
    ->menu(['text' => __('Answers'), 'link' => ['comments/answerlist']])
    ->menu(['text' => __('Settings'), 'link' => ['comments/settings']])
    ->display(); ?>