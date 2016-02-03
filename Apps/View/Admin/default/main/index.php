<?php

/** @var array $stats */

$this->title = __('FFCMS 3 Dashboard');
$this->breadcrumbs = [
    __('Main')
]
?>
<h1>Main dashboard</h1>
<hr/>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">General info</div>
            <div class="panel-body">
                <?= \Ffcms\Core\Helper\HTML\Table::display([
                    'table' => ['class' => 'table table-bordered'],
                    'tbody' => [
                        'items' => [
                            [
                                ['text' => 'FFCMS version'],
                                ['text' => $stats['ff_version']]
                            ],
                            [
                                ['text' => 'PHP version'],
                                ['text' => $stats['php_version']]
                            ],
                            [
                                ['text' => 'OS name'],
                                ['text' => $stats['os_name']]
                            ],
                            [
                                ['text' => 'Database name'],
                                ['text' => $stats['database_name']]
                            ],
                            [
                                ['text' => 'Files size'],
                                ['text' => $stats['file_size']]
                            ],
                            [
                                ['text' => 'Load average'],
                                ['text' => $stats['load_avg']]
                            ],
                        ]
                    ]
                ]); ?>
                <?= \Ffcms\Core\Helper\Url::link(['main/cache'], 'Clean cache', ['class' => 'btn btn-warning']) ?>
                <?= \Ffcms\Core\Helper\Url::link(['main/sessions'], 'Clean sessions', ['class' => 'btn btn-info']) ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">FFCMS News</div>
            <div class="panel-body">news there</div>
        </div>
    </div>
</div>