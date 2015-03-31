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
    'ul' => [],
    'li' => [],
    'items' => [
        'Text1', 'text2', 'text3',
        'main/index' => 'Text4'
    ]
]); ?>