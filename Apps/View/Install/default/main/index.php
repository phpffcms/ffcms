<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var $model \Apps\Model\Install\Main\EntityCheck */

$this->layout('_layouts/default', [
    'title' => 'Install ffcms'
])

?>

<?php $this->start('body') ?>

<h1><?= __('Prepare to install') ?></h1>
<p><?= __('Welcome in FFCMS 3 installer. Before you start installation you must have:') ?></p>
<ul>
    <li><?= __('Working database server and exist database connection data(db name, user, password, host)') ?></li>
    <li><?= __('Working web-server based on apache2 or nginx') ?></li>
    <li><?= __('PHP version 7.1 or later, mod-rewrite or nginx rewrite, GD lib 2 or later, PDO driver') ?></li>
    <li><?= __('Correct chmod for directories') ?></li>
</ul>
<p><?= __('Remember, you can run installation from console and many actions will be done automatic') ?>.</p>
<h2><?= __('Checking web-server') ?></h2>
<?php
    $table = $this->table(['class' => 'table table-bordered'])
        ->head([
            ['text' => __('Service')],
            ['text' => __('Version/Status')]
        ]);
    $table->row([
        ['text' => 'PHP'],
        ['text' => $model->phpVersion],
        'properties' => ['class' => $model->checkPhpVersion() ? 'bg-success' : 'bg-danger']
    ]);
    $table->row([
        ['text' => 'PHP::PDO'],
        ['text' => $model->pdo ? 'On' : 'Off'],
        'properties' => ['class' => $model->pdo ? 'bg-success' : 'bg-danger'],
    ]);
    $table->row([
        ['text' => 'PHP::GD'],
        ['text' => $model->gd ? 'On' : 'Off'],
        'properties' => ['class' => $model->gd ? 'bg-success' : 'bg-danger']
    ])
?>
<div class="table-responsive">
    <?= $table->display() ?>
</div>

<h2><?= __('Checking chmod') ?></h2>
<?php
$chmodItems = [];
foreach ($model->chmodCheck as $dir => $status) {
    $chmodItems[] = [
        ['text' => $dir], ['text' => $status ? 'Ok' : 'Error'], 'properties' => ['class' => $status ? 'bg-success' : 'bg-danger']
    ];
}
?>
<div class="table-responsive">
    <?= $this->table(['class' => 'table table-bordered'])
        ->head([
            ['text' => __('Path')],
            ['text' => __('Status')]
        ])->body($chmodItems)
        ->display();
    ?>
</div>

<h2 id="license">The MIT License</h2>
<textarea class="form-control" rows="5">
Copyright (c) 2015-<?= date('Y') ?> FFCMS, Mihail Pyatinskiy

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
SOFTWARE.</textarea>
<br />
<?php if($model->checkAll()): ?>
    <div class="row">
        <div class="col-3" style="padding-top: 5px;padding-left: 25px;">
            <input type="checkbox" id="agree-license" /> <label for="agree-license"><?= __('Accept license') ?></label>
        </div>
        <div class="col-9">
            <a href="#license" class="btn btn-success w-100 disabled" id="install-link"><?= __('Start install') ?></a>
        </div>
    </div>
<?php else: ?>
    <?= \Ffcms\Templex\Url\Url::a(['main/index'], __('Check again'), ['class' => 'btn btn-warning w-100']) ?>
<?php endif; ?>
    <script>
        $(document).ready(function () {
            $('#agree-license').change(function () {
                if ($(this).is(':checked')) {
                    $('#install-link').removeClass('disabled').attr('href', '<?= \Ffcms\Templex\Url\Url::to('main/install') ?>');
                } else {
                    $('#install-link').addClass('disabled').attr('href', '#license');
                }
            });
        });
    </script>

<?php $this->stop() ?>