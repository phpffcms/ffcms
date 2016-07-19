<?php
use Ffcms\Core\Helper\HTML\Bootstrap\Nav;
use Ffcms\Core\Helper\HTML\Bootstrap\Navbar;
use Ffcms\Core\Helper\HTML\Listing;
use Ffcms\Core\Helper\HTML\Table;

/** @var $this \Ffcms\Core\Arch\View */

$this->title = __('Test title');
?>

<p>Hello! This is a developer demonstration page with some ffcms features. Fore more information please read: <a href="https://doc.ffcms.ru">ffcms docs</a></p>
<p>Current file location: <b>/Apps/View/Front/default/main/index.php</b></p>

<!-- Example of including other blocks -->
<div class="row">
    <div class="col-md-6">
        <?= $this->render('main/block/test1') ?>
    </div>
    <div class="col-md-6">
        <?= $this->render('main/block/test2', ['var' => 'var value']) ?>
    </div>
</div>

<hr />

<!-- Example internalization. File with translation: /Apps/View/Front/default/i18n/ru.php -->
<?= __('Example of usage internalization in template. Test var: %var%', ['var' => 'some value']) ?>

<hr />

<!-- Example of usage listing builder -->
<?= Listing::display([
    'type' => 'ul',
    'property' => ['id' => 'primary'],
    'items' => [
        ['type' => 'text', 'text' => 'Text li item', 'property' => ['class' => 'text-text'], 'html' => false],
        ['type' => 'link', 'link' => ['main/index'], 'text' => 'Link li item', 'property' => ['class' => 'text-text'], 'html' => false, 'activeClass' => 'active'],
        ['type' => 'link', 'link' => ['main/read', 'somePath1', 'somePath2', ['a' => 'wtf', 'd' => 'test']], 'text' => 'Link li item with params', 'html' => false, 'linkProperty' => ['id' => 'link1', 'class' => 'btn btn-info']]
    ]
]); ?>

<hr />

<!-- Example of usage navigation builder -->
<?= Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'tabAnchor' => 'n',
    'items' => [
        ['type' => 'link', 'text' => 'Link to main', 'link' => ['main/index', 'test']],
        ['type' => 'tab', 'text' => 'Tab 1', 'content' => 'This is tab 1 content with allowed <s>html</s> data!', 'htmlContent' => true],
        ['type' => 'tab', 'text' => 'Tab 2', 'content' => 'This is tab 2 content'],
        ['type' => 'tab', 'text' => 'Tab 3', 'content' => 'This is a tab 3 content']
    ]
]); ?>

<hr />

<!-- Example of usage navbar builder -->
<?= Navbar::display([
    'nav' => ['class' => 'navbar-default'],
    'property' => ['id' => 'headmenu', 'class' => 'navbar-nav'],
    'brand' => ['link' => 'main/to', 'text' => 'FFCMS'],
    'collapseId' => 'collapse-object',
    'items' => [
        ['link' => ['main/index'], 'text' => 'Link 1', 'property' => ['class' => 'test1'], 'position' => 'left'],
        ['link' => 'main/other', 'text' => 'Link 2', 'position' => 'left'],
        ['link' => 'main/read', 'text' => 'Link 7', 'position' => 'right'],
        'plaintext'
    ]
]); ?>

<hr />

<!-- Example of usage Table builder -->
<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => 'id'],
            ['text' => 'name'],
            ['text' => 'family']
        ],
        'property' => ['id' => 'thead_main']
    ],
    'tbody' => [
        'property' => ['id' => 'tbodym'],
        'items' => [
            [['text' => '0'], ['text' => 'Ivan'], ['text' => 'Ivanov']], // auto order item
            [0 => ['text' => '1', 'property' => ['class' => 'test-td']], 1 => ['text' => 'Ivan'], 2 => ['text' => 'Petrov'], 'property' => ['class' => 'g-class']], // hard ordered item
            [['text' => '2'], ['text' => '<b>Petr^</b>', 'html' => true], ['text' => 'Groznyi']]
        ]
    ]
]); ?>