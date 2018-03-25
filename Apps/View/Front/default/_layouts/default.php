<?php
/** @var Ffcms\Templex\Template\Template $this */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?? 'no title'; ?></title>
    <?= $this->section('css') ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="<?= \App::$Alias->currentViewUrl ?>/assets/css/style.css" />
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" />
    <script src="<?= \App::$Alias->scriptUrl ?>/vendor/components/jquery/jquery.min.js"></script>
    <?php if (\App::$Debug): ?>
        <?= \App::$Debug->renderHead() ?>
    <?php endif; ?>
</head>
<body class="bg-light">


<?= $this->bootstrap()->navbar(['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top'], true) // (properties, container=false)
->id('my-navbar')
    ->brand(['text' => 'My website'])
    ->menu('left', ['text' => 'Item #1', 'link' => ['controller/action']])
    ->menu('left', ['text' => 'Item #2', 'link' => ['controller/action']])
    ->menu('left', ['text' => 'Item #3', 'link' => ['controller/action']])
    ->menu('left', ['text' => 'Item #4', 'dropdown' => [
        ['text' => 'Item #4-1', 'link' => ['con/act1'], 'class' => 'dropdown-item'],
        ['text' => 'Item #4-2', 'link' => ['sdf/act2'], 'class' => 'dropdown-item']
    ]])
    ->menu('right', ['text' => 'Item #1', 'link' => ['controller/action']])
    ->menu('right', ['text' => 'Item #2', 'dropdown' => [
        ['text' => 'Item #2-1', 'link' => ['con/act1'], 'class' => 'dropdown-item'],
        ['text' => 'Item #2-2', 'link' => ['sdf/act2'], 'class' => 'dropdown-item']
    ]])
    ->display()
?>

<header class="container">
    <div class="row">
        <div class="col-md-1">
            <img src="<?= \App::$Alias->currentViewUrl ?>/assets/img/logo.png" alt="logo" class="img-fluid" />
        </div>
        <div class="col-md-7">
            <div class="h1 mb-0">FFCMS Demo</div>
            <small class="text-secondary">Some website description text</small>
        </div>
        <div class="col">
            <form class="form-inline">
                <input type="text" class="form-control col-md-9 mr-md-2" id="searchInput" placeholder="query...">
                <button type="submit" class="btn btn-primary col-md">Submit</button>
            </form>
        </div>
    </div>
</header>

<main role="main" class="container">
    <div class="row">
        <div class="col-md-9">
            <?php if($this->section('body')): ?>
                <?= $this->section('body') ?>
            <?php else: ?>
                <p>Page not found!</p>
            <?php endif; ?>
        </div>
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    Widget title
                </div>
                <div class="card-body">
                    <p>Widget content</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->section('javascript') ?>

<script src="http://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>


<?php if (\App::$Debug): ?>
    <?= \App::$Debug->renderOut() ?>
<?php endif; ?>
</body>
</html>