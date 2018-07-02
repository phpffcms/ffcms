<?php

use Ffcms\Core\Helper\Type\Any;
use Ffcms\Templex\Url\Url;

/** @var $routes array */
/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('Routing'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        __('Routing')
    ]
]);
$aliasExist = false;
$callbackExist = false;
?>

<?php $this->start('body') ?>
<h1><?= __('Routing scheme') ?></h1>
<hr/>
<div class="row">
    <div class="col-md-6">
        <h2><?= __('Static(alias) routes') ?></h2>
        <?php
        if ($routes['Alias'] && Any::isArray($routes['Alias']) && count($routes['Alias']) > 0) {
            $aliasExist = true;
            echo '<div class="table-responsive">';
            $alias = $this->table(['class' => 'table'])
                ->head([
                    ['text' => "Source → Target"],
                    ['text' => __('Environment')],
                    ['text' => '']
                ]);
            foreach ($routes['Alias'] as $env => $route) {
                if (Any::isArray($route)) {
                    foreach ($route as $source => $target) {
                        $alias->row([
                            ['text' => '<span class="badge badge-primary">' . $source . '</span> ' .
                                '→ ' .
                                '<span class="badge badge-secondary">' . $target . '</span>', 'html' => true],
                            ['text' => $env],
                            ['text' => Url::a(
                                ['main/deleteroute', null, ['type' => 'Alias', 'loader' => $env, 'path' => $source]],
                                '<i class="fa fa-remove"></i>',
                                ['html' => true]
                            ), 'properties' => ['class' => 'text-center'], 'html' => true]
                        ]);
                    }
                }
            }
            echo $alias->display();
            echo '</div>';
        }
        ?>
    </div>
    <div class="col-md-6">
        <h2><?= __('Dynamic(callback) routes') ?></h2>
        <?php
        if ($routes['Callback'] && Any::isArray($routes['Callback']) && count($routes['Callback']) > 0) {
            $callbackExist = true;
            echo '<div class="table-responsive">';
            $dynamic = $this->table(['class' => 'table'])
                ->head([
                    ['text' => "Source → Target"],
                    ['text' => __('Environment')],
                    ['text' => '']
                ]);
            foreach ($routes['Callback'] as $env => $route) {
                if (Any::isArray($route)) {
                    foreach ($route as $source => $target) {
                        $dynamic->row([
                            ['text' => '<span class="badge badge-primary">' . $source . '</span> ' .
                                '→ ' .
                                '<span class="badge badge-secondary">' . $target . '</span>', 'html' => true],
                            ['text' => $env],
                            ['text' => Url::a(
                                ['main/deleteroute', null, ['type' => 'Callback', 'loader' => $env, 'path' => $source]],
                                '<i class="fa fa-remove"></i>',
                                ['html' => true]
                            ), 'properties' => ['class' => 'text-center'], 'html' => true]
                        ]);
                    }
                }
            }
            echo $dynamic->display();
            echo '</div>';
        }
        ?>
    </div>
</div>
<?php if (!$aliasExist && !$callbackExist): ?>
    <p class="alert alert-warning"><?= __('Custom routes is not yet found') ?></p>
<?php endif ;?>
<?= Url::a(['main/addroute'], '<i class="fa fa-plus"></i> ' . __('New route'), ['class' => 'btn btn-primary', 'html' => true]) ?>
<?php $this->stop() ?>
