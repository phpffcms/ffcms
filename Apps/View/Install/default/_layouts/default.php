<?php

/** @var Ffcms\Templex\Template\Template $this */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?? 'no title'; ?></title>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/components/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?= \App::$Alias->currentViewUrl ?>/assets/css/style.css" />
    <?php if (\App::$Properties->get('multiLanguage') && count(\App::$Properties->get('languages')) > 1): ?>
        <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/language-flags/flags.css" />
    <?php endif; ?>

    <?= $this->section('css') ?>
    <!-- jquery usage after-load logic -->
    <script>(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y);}else{w.readyQ.push(x);}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script>
    <script>
        var script_url = '<?= \App::$Alias->scriptUrl ?>';
        var script_lang = '<?= \App::$Request->getLanguage() ?>';
        var site_url = '<?= \App::$Alias->baseUrl ?>';
    </script>
    <?php if (!isset($fullgrid)){ $fullgrid = false; } ?>
</head>

<body>

<header class="container">
    <div class="row">
        <div class="col-md-2">
            <img src="<?= \App::$Alias->currentViewUrl ?>/assets/img/logo.png" alt="logo" class="img-responsive" />
        </div>
        <div class="col-md-10">
            <h1>FFCMS 3</h1>
            <small><?= __('Fast, flexibility content management system with MVC framework inside!') ?></small>
            <?php if (\App::$Properties->get('multiLanguage') && count(\App::$Properties->get('languages')) > 1) {
                $list = $this->listing('ul', ['class' => 'list-inline']);
                foreach (\App::$Properties->get('languages') as $lang) {
                    $list->li([
                        'text' => '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="flag flag-' . $lang . '" alt="' . $lang . '">',
                        'link' => App::$Alias->baseUrlNoLang . '/' . $lang . App::$Request->getPathInfo(),
                        'html' => true
                    ], ['class' => 'list-inline-item']);
                }
                echo $list->display();
            } ?>
        </div>
    </div>
</header>

<main role="main" class="container body-container">
    <div class="row">
        <div class="col-12">
            <?php
            if ($this->section('body')) {
                // display notifications if exist
                $notifyMessages = \App::$Session->getFlashBag()->all();
                if (\Ffcms\Core\Helper\Type\Any::isArray($notifyMessages) && count($notifyMessages) > 0) {
                    foreach ($notifyMessages as $mType => $mArray) {
                        if ($mType === 'error') {
                            $mType = 'danger';
                        }
                        foreach ($mArray as $mText) {
                            echo $this->bootstrap()->alert($mType, $mText);
                        }
                    }
                }
                echo $this->section('body');
            } else {
                echo '<p>Page not found!</p>';
            }
            ?>
        </div>
    </div>
</main>

<footer class="container mt-md-3">
    <div class="row">
        <div class="col-md-12">
            <p>&copy; <?= date('Y') ?> website. Powered on <a href="https://ffcms.org">ffcms.org</a>.</p>
        </div>
    </div>
</footer>

<?= $this->section('javascript') ?>

<script src="<?= \App::$Alias->scriptUrl ?>/vendor/components/jquery/jquery.min.js"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/popper.js/dist/umd/popper.min.js"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- jQuery code interprier after library loaded -->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
</body>
</html>