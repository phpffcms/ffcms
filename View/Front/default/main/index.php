<?php
    /** @var $model \Model\Front\User */
    /** @var $this \Core\Arch\View */
    echo $this->show('main/other', ['model' => $model]);
    $this->title = 'Welcome to web-site"\'';
?>
<p>Welcome, <?php echo $model->getLabel('name'); ?>: <?php echo $model->name; ?>. We know you like a <?php echo $model->role; ?></p>
<?php echo __('Test text %a% var', ['a' => 'success']); ?>
<br />
<?php echo \Core\Helper\HTML\Listing::display([
    'type' => 'ul',
    'ul' => ['id' => 'primary'],
    'items' => [
        ['type' => 'text', 'text' => 'Awesome text', 'property' => ['class' => 'text-text'], 'html' => false],
        ['type' => 'link', 'link' => ['main/index', 5, 2], 'text' => 'My link!', 'property' => ['class' => 'text-text'], 'html' => false, 'activeClass' => 'active'],
        ['type' => 'link', 'link' => ['main/read', null, null, ['a' => 'f', 'd' => 'test']], 'text' => 'My other link', 'html' => false, 'linkProperty' => ['id' => 'link1', 'class' => 'btn btn-info']]
    ]
]); ?>

<br />


<?php echo \Core\Helper\HTML\Bootstrap\Nav::display([
    'ul' => 'nav-tabs',
    'tabAnchor' => 'n',
    'items' => [
        ['type' => 'link', 'text' => 'Welcome to hell', 'link' => 'main/other'],
        ['type' => 'tab', 'text' => 'Tabbed item', 'content' => 'This is a full content of current tab!'],
        ['type' => 'tab', 'text' => 'Other tab', 'content' => 'This is an other content of other tab!']
    ]
]); ?>

<br />

<? echo \Core\Helper\HTML\Bootstrap\Navbar::display([
    'nav' => ['class' => 'navbar-default'],
    'ul' => ['id' => 'headmenu', 'class' => 'navbar-nav'],
    'brand' => ['link' => 'main/to', 'text' => 'FFCMS'],
    'collapseId' => 'collapse-object',
    'items' => [
        ['link' => ['main/index'], 'text' => 'Link 1', 'property' => ['class' => 'test1'], 'position' => 'left'],
        ['link' => 'main/other', 'text' => 'Link 2', 'position' => 'left'],
        ['link' => 'main/read', 'text' => 'Link 7', 'position' => 'right'],
        '<form class="navbar-form navbar-left" role="search"><div class="form-group"><input type="text" class="form-control" placeholder="Search"></div><button type="submit" class="btn btn-default">Submit</button></form>'
    ]
]); ?>