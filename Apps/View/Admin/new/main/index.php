<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Install\Main\EntityCheck $check */
/** @var array $stats */
/** @var boolean $tokenActive */
/** @var array $yandexCfg */
/** @var array|null $visits */
/** @var array|null $sources */

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Main')
]);

?>

<?php $this->start('body'); ?>

<h1><?= __('Main dashboard') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Dashboard')
]]) ?>

<div class="row">
    <div class="col-md-4">
        <h3><?= __('Server info') ?></h3>
        <div class="table-responsive">
            <?= $this->table(['class' => 'table'])
                ->row([
                    ['text' => __('FFCMS version')],
                    ['text' => $stats['ff_version']]
                ])
                ->row([
                    ['text' => __('PHP version')],
                    ['text' => $stats['php_version']]
                ])
                ->row([
                    ['text' => __('OS name')],
                    ['text' => $stats['os_name']]
                ])
                ->row([
                    ['text' => __('Database')],
                    ['text' => $stats['database_name']]
                ])
                ->row([
                    ['text' => __('Files size')],
                    ['text' => $stats['file_size']]
                ])
                ->row([
                    ['text' => __('Load average')],
                    ['text' => $stats['load_avg']]
                ])
                ->display(); ?>
        </div>
    </div>
    <div class="col-md-4">
        <h3><?= __('Directories and files') ?></h3>
        <?php
        foreach ($check->chmodCheck as $dir => $status) {
            echo $this->bootstrap()->badge(($status ? 'success' : 'danger'), $dir) . "&nbsp;";
        }
        ?>
        <hr />
        <p><?= __('All directories and files in this list required to be readable and writable.') ?></p>
        <hr />
        <?= $this->bootstrap()->button('a', __('Clear cache'), ['href' => Url::to('main/cache'), 'class' => 'btn-warning']) ?>
        <?= $this->bootstrap()->button('a', __('Clear sessions'), ['href' => Url::to('main/sessions'), 'class' => 'btn-info']) ?>
    </div>
    <div class="col-md-4">
        <h3><?= __('FFCMS News') ?></h3>
        <ul id="ffcms-news-list">
            <li>No internet connection</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md">
        <h2 class="mt-2"><?= __('Yandex.Metrika') ?></h2>
        <hr />
        <?php if (!$tokenActive): ?>
            <?= $this->bootstrap()->button('a', __('Connect Yandex.Metrika'), [
                'href' => Url::to('main/yandexconnect'),
                'class' => 'btn-danger'
            ]) ?>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <canvas id="visitChart" width="100%" height="50"></canvas>
                </div>
                <div class="col-md-4">
                    <canvas id="sourcesChart" width="100%" height="90"></canvas>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>
<?php if ($tokenActive): ?>
<script type="text/javascript" src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/chart.js/dist/Chart.min.js"></script>
<?php
// visit chart
$dates = [];
$views = [];
$users = [];
$bounce = [];
foreach ($visits as $date => $info) {
    $dates[] = Date::convertToDatetime($date, Date::FORMAT_TO_DAY);
    $views[] = $info['views'];
    $users[] = $info['users'];
    $bounce[] = $info['bounce'];
}

// sources chart
$sourceTypes = array_keys($sources);
$sourceUsers = array_values($sources);

?>
<script>
    var visitElement = $('#visitChart').get(0);
    var sourcesElement = $('#sourcesChart').get(0);
    var chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(231,233,237)'
    };

    var visitChart = new Chart(visitElement, {
        type: 'line',
        data: {
            labels: [<?php foreach ($dates as $date){ echo '"' . $date . '", '; } ?>],
            datasets: [{
                yAxisID: 'units',
                label: '<?= __('Views') ?>',
                borderColor: chartColors.blue,
                fill: false, // no background color
                data: [<?= implode(',', $views) ?>]
            }, {
                yAxisID: 'units',
                label: '<?= __('Users') ?>',
                borderColor: chartColors.green,
                fill: false, // no background color
                data: [<?= implode(',', $users) ?>]
            }, {
                yAxisID: 'percentage',
                label: '<?= __('Bounces, %') ?>',
                borderColor: chartColors.red,
                fill: false, // no background color
                data: [<?= implode(',', $bounce) ?>]
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: true
            },
            title: {
                display: true,
                text: '<?= __('Users, page views, bounces - 30 days') ?>'
            },
            scales: {
                yAxes: [{
                    id: 'units',
                    type: 'linear',
                    position: 'left'
                }, {
                    id: 'percentage',
                    type: 'linear',
                    position: 'right'
                }]
            }
        }
    });

    var sourceChart = new Chart(sourcesElement, {
        type: 'pie',
        data: {
            labels: [<?php foreach ($sourceTypes as $type){ echo '"' . $type . '", '; } ?>],
            datasets: [{
                data: [<?= implode(',', $sourceUsers) ?>],
                label: 'Users',
                backgroundColor: Object.values(chartColors)
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: '<?= __('Traffic sources - 30 days') ?>'
            }
        }
    });
</script>
<?php endif; ?>
<script>
    $(document).ready(function(){
        $.getJSON(script_url + '/api/main/news?lang=' + script_lang, function (resp) {
            if (resp.status !== 1) {
                return;
            }
            $('#ffcms-news-list').empty();
            $.each(resp.data, function (key, news) {
                $('<li>').html($('<a>', {
                    href: news.url,
                    target: '_blank',
                    text: news.title
                })).appendTo('#ffcms-news-list');
            });
        });
    });
</script>
<?php $this->stop() ?>
