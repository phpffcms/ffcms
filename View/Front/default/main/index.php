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
        ['type' => 'tab', 'text' => 'Tabbed item', 'content' => 'This is a full content <s>of current</s> tab!', 'htmlContent' => true],
        ['type' => 'tab', 'text' => 'Other tab', 'content' => 'This is an other content of other tab!']
    ]
]); ?>

<br />

<?php echo \Core\Helper\HTML\Bootstrap\Navbar::display([
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


<br />

<?php echo \Core\Helper\HTML\Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => 'id'],
            ['text' => 'name <i class="fa fa-at"></i>', 'html' => true],
            ['text' => 'family']
        ],
        'property' => ['id' => 'thead_main']
    ],
    'tbody' => [
        'property' => ['id' => 'tbodym'],
        'items' => [
            [0 => ['text' => '0', 'property' => ['class' => 'test-td']], 1 => ['text' => 'Ivan'], 2 => ['text' => 'Putin'], 'property' => ['class' => 'g-class']],
            [['text' => '1'], ['text' => '<b>Petr^</b>', 'html' => true], ['text' => 'Groznyi']]
        ]
    ]
]); ?>

<br />

<?php $form = new \Core\Helper\HTML\Form([
    'model' => $model,
    'property' => ['id' => 'test-form', 'class' => 'form-horizontal', 'method' => 'post', 'action' => ''],
    'name' => 'main-form',
    'structure' => '<div class="form-group"><label for="%name%" class="col-md-3 control-label">%label%</label><div class="col-md-9">%item% <p class="help-block">%help%</p></div></div>',
]); ?>
<?php echo $form->field('name', 'inputText', ['class' => 'form-control'], __('Helper block for current param')); ?>
<?php echo $form->field('role', 'select', ['class' => 'form-control', 'options' => ['admin', 'guest', 'user']]); ?>
<?php echo $form->field('isJoined', 'checkbox'); ?>
<?php echo $form->submitButton('Submit it', ['class' => 'btn btn-success']); ?>
<?php $form->finish(); ?>