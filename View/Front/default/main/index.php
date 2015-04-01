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

    <div role="tabpanel">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
            <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Messages</a></li>
            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">...</div>
            <div role="tabpanel" class="tab-pane" id="profile">...</div>
            <div role="tabpanel" class="tab-pane" id="messages">...</div>
            <div role="tabpanel" class="tab-pane" id="settings">...</div>
        </div>

    </div>


<?php echo \Core\Helper\HTML\Bootstrap\Nav::display([
    'ul' => 'nav-tabs',
    'tabAnchor' => 'n',
    'items' => [
        ['type' => 'link', 'text' => 'Welcome to hell', 'link' => ['main/ful']],
        ['type' => 'tab', 'text' => 'Tabbed item', 'content' => 'This is a full content of current tab!'],
        ['type' => 'tab', 'text' => 'Other tab', 'content' => 'This is an other content of other tab!']
    ]
]); ?>