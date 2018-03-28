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
    <!-- jquery usage after-load logic -->
    <script>(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y);}else{w.readyQ.push(x);}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script>
    <script>
        var script_url = '<?= \App::$Alias->scriptUrl ?>';
        var script_lang = '<?= \App::$Request->getLanguage() ?>';
        var site_url = '<?= \App::$Alias->baseUrl ?>';
    </script>
    <?php if (\App::$Debug): ?>
        <?= \App::$Debug->renderHead() ?>
    <?php endif; ?>
</head>
<body class="bg-light">

<?php
$navbar = $this->bootstrap()->navbar(['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top'], true);
$navbar->brand(['text' => __('Home'), 'link' => ['main/index']]);
$navbar->menu('left', ['text' => __('News'), 'link' => ['content/list', ['news']]]);
$navbar->menu('left', ['text' => __('About'), 'link' => ['content/read', ['page', 'about-page']]]);
$navbar->menu('left', ['text' => __('Feedback'), 'link' => ['feedback/create']]);
$navbar->menu('left', ['text' => __('Users'), 'link' => ['profile/index', ['all']]]);

if (\App::$User->isAuth()) {
    $userId = \App::$User->identity()->getId();
    $navbar->menu('right', ['text' => __('Account'), 'dropdown' => [
        ['text' => __('My profile'), 'link' => ['profile/show', [$userId]]],
        ['text' => __('Messages'), 'link' => ['profile/messages'], 'class' => 'dropdown-item'],
        ['text' => __('Feed'), 'link' => ['profile/feed'], 'class' => 'dropdown-item'],
        ['text' => __('Notifications'), 'link' => ['profile/notifications'], 'class' => 'dropdown-item'],
        ['text' => __('Settings'), 'link' => ['profile/settings'], 'class' => 'dropdown-item'],
    ]]);
    $navbar->menu('right', ['text' => __('Logout'), 'link' => ['user/logout']]);
} else {
    $navbar->menu('right', ['text' => __('Sign in'), 'link' => ['user/login']]);
    $navbar->menu('right', ['text' => __('Sign up'), 'link' => ['user/signup']]);
}

echo $navbar->display();
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

<footer class="container mt-md-3">
    <div class="row">
        <div class="col-md-12">
            Test
        </div>
    </div>
</footer>

<?= $this->section('javascript') ?>

<script src="<?= \App::$Alias->scriptUrl ?>/vendor/components/jquery/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- jQuery code interprier after library loaded -->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>

<?php if (\App::$Debug): ?>
    <?= \App::$Debug->renderOut() ?>
<?php endif; ?>
</body>
</html>