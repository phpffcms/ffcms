<?php

/** @var \Ffcms\Templex\Template\Template $this */

echo $this->bootstrap()->nav('ul', ['class' => 'nav-tabs'])
    ->menu(['text' => __('New request'), 'link' => ['feedback/create']])
    ->menu(['text' => __('My requests'), 'link' => ['feedback/list']])
    ->display();