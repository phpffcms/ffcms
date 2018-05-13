<?php

/** @var Ffcms\Templex\Template\Template $this */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?? 'no title'; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?= \App::$Alias->currentViewUrl ?>/assets/css/style.css" />
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/components/font-awesome/css/font-awesome.min.css" />
    <?= $this->section('css') ?>
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
    <?php if (!isset($fullgrid)){ $fullgrid = false; } ?>
</head>
<body class="bg-light">

<?php
$navbar = $this->bootstrap()->navbar(['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top'], true);
$navbar->brand(['text' => __('Home'), 'link' => ['/']]);
$navbar->menu('left', ['text' => __('News'), 'link' => ['content/list', ['news']]]);
$navbar->menu('left', ['text' => __('About'), 'link' => ['content/read', ['page', 'about-page']]]);
$navbar->menu('left', ['text' => __('Feedback'), 'link' => ['feedback/create']]);
$navbar->menu('left', ['text' => __('Users'), 'link' => ['profile/index', ['all']]]);

if (\App::$User->isAuth()) {
    $userId = \App::$User->identity()->getId();
    $navbar->menu('right', ['text' => __('Account') . ' <span class="badge" id="summary-count-block">0</span>', 'dropdown' => [
        ['text' => __('My profile'), 'link' => ['profile/show', [$userId]]],
        ['text' => __('Messages') . ' <span class="badge" id="pm-count-block">0</span>', 'link' => ['profile/messages'], 'class' => 'dropdown-item', 'html' => true],
        ['text' => __('Feed'), 'link' => ['profile/feed'], 'class' => 'dropdown-item'],
        ['text' => __('Notifications') . ' <span class="badge" id="notify-count-block">0</span>', 'link' => ['profile/notifications'], 'class' => 'dropdown-item', 'html' => true],
        ['text' => __('Settings'), 'link' => ['profile/settings'], 'class' => 'dropdown-item'],
    ], 'properties' => ['html' => true]]);
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
    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <?php
                $crumbs = $this->listing('ol', ['class' => 'breadcrumb']);
                foreach ($breadcrumbs as $url => $text) {
                    if (\Ffcms\Core\Helper\Type\Any::isInt($url)) {
                        $crumbs->li($text, ['class' => 'breadcrumb-item active']);
                    } else {
                        $crumbs->li(['link' => $url, 'text' => $text], ['class' => 'breadcrumb-item']);
                    }
                }
                echo $crumbs->display();
                ?>
            </nav>
        </div>
    </div>
    <?php endif; ?>
    <div class="row">
        <div class="<?= ($fullgrid ? 'col-md-12' : 'col-md-9') ?>">
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
        <?php if (!$fullgrid): ?>
        <div class="col-md">
            <?php if (\Widgets\Front\Newcontent\Newcontent::enabled()): ?>
                <div class="card">
                    <div class="card-header">
                        <?= __('New content') ?>
                    </div>
                    <div class="card-body">
                        <?= \Widgets\Front\Newcontent\Newcontent::widget() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
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
<script src="http://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= \App::$Alias->currentViewUrl ?>/assets/js/global.js"></script>

<!-- jQuery code interprier after library loaded -->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>

<?php if (\App::$Debug): ?>
    <?= \App::$Debug->renderOut() ?>
<?php endif; ?>
</body>
</html>