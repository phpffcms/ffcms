<?php
/** @var \Ffcms\Templex\Template\Template $this */

echo $this->bootstrap()->nav('ul', ['class' => 'nav-tabs mb-2'])
    ->menu(['text' => __('Log In'), 'link' => ['user/login']])
    ->menu(['text' => __('Sign Up'), 'link' => ['user/signup']])
    ->menu(['text' => __('Recovery'), 'link' => ['user/recovery']])
    ->display();