<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Install\Main\EntityCheck $check */
/** @var array $stats */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Main')
])
?>

<?php $this->start('body'); ?>

<h1><?= __('Main dashboard') ?></h1>
<hr />
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
        <?= $this->bootstrap()->button('a', __('Clear sessions'), ['href' => Url::to('main/cache'), 'class' => 'btn-info']) ?>
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
        <h2 class="mt-2"><?= __('Summary statistics') ?></h2>
        <hr />
        <?= $this->bootstrap()->button('a', 'Connect Yandex.Metrika', ['link' => '#', 'class' => 'btn-danger']) ?>
    </div>
</div>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>
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
