<?php
/** @var $model \Apps\Model\Install\Main\EntityCheck */
?>
<h1><?= __('Prepare to install') ?></h1>
<hr />
<p><?= __('Welcome in FFCMS 3 installer. Before you start installation you must have:') ?></p>
<ul>
    <li><?= __('Working database server and exist database connection data(db name, user, password, host)') ?></li>
    <li><?= __('Working web-server based on apache2 or nginx') ?></li>
    <li><?= __('PHP version 5.4 or later, mod-rewrite or nginx rewrite, GD lib 2 or later, PDO driver') ?></li>
    <li><?= __('Correct chmod for directories') ?></li>
</ul>
<p><?= __('Remember, you can run installation from console and many actions will be done automatic') ?>.</p>
<h2><?= __('Checking web-server') ?></h2>
<hr />
<?= \Ffcms\Core\Helper\HTML\Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => __('Service')],
            ['text' => __('Version/Status')]
        ]
    ],
    'tbody' => [
        'items' => [
            [['text' => 'PHP'], ['text' => $model->phpVersion], 'property' => ['class' => $model->checkPhpVersion() ? 'alert-success' : 'alert-danger']],
            [['text' => 'PHP::PDO'], ['text' => $model->pdo ? 'On' : 'Off'], 'property' => ['class' => $model->pdo ? 'alert-success' : 'alert-danger']],
            [['text' => 'PHP::GD'], ['text' => $model->gd ? 'On' : 'Off'], 'property' => ['class' => $model->gd ? 'alert-success' : 'alert-danger']]
        ]
    ]
]);
?>
<h2><?= __('Checking chmod') ?></h2>
<hr />
<?php
$chmodItems = [];
foreach ($model->chmodCheck as $dir => $status) {
    $chmodItems[] = [
        ['text' => $dir], ['text' => $status ? 'Ok' : 'Error'], 'property' => ['class' => $status ? 'alert-success' : 'alert-danger']
    ];
}
?>
<?= \Ffcms\Core\Helper\HTML\Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => __('Path')],
            ['text' => __('Status')]
        ]
    ],
    'tbody' => [
        'items' => $chmodItems
    ]
]);
?>

<?php if($model->checkAll()): ?>
    <?= \Ffcms\Core\Helper\Url::link('main/install', __('Start install'), ['class' => 'btn btn-success btn-block']) ?>
<?php else: ?>
    <?= \Ffcms\Core\Helper\Url::link('main/index', __('Check again'), ['class' => 'btn btn-warning btn-block']) ?>
<?php endif; ?>

