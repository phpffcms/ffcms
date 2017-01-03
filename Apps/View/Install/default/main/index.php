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

<h2 id="license">The MIT License</h2>
<textarea class="form-control" rows="5">
Copyright (c) 2015-2016 FFCMS, Mihail Pyatinskyi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
</textarea>

<br />

<?php if($model->checkAll()): ?>
    <div class="row">
        <div class="col-xs-3" style="padding-top: 5px;padding-left: 25px;">
            <input type="checkbox" id="agree-license" /> <label for="agree-license"><?= __('Accept license') ?></label>
        </div>
        <div class="col-xs-9">
            <a href="#license" class="btn btn-success btn-block" id="install-link" disabled="disabled"><?= __('Start install') ?></a>
        </div>
    </div>
<?php else: ?>
    <?= \Ffcms\Core\Helper\Url::link('main/index', __('Check again'), ['class' => 'btn btn-warning btn-block']) ?>
<?php endif; ?>
<script>
    document.ready(function () {
        $('#agree-license').change(function () {
            if ($(this).is(':checked')) {
                $('#install-link').attr('disabled', false).attr('href', '<?= \Ffcms\Core\Helper\Url::to('main/install') ?>');
            } else {
                $('#install-link').attr('disabled', true).attr('href', '#license');
            }
        });
    });
</script>

