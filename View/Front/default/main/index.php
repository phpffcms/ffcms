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