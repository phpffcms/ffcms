<?php

/** @var Ffcms\Templex\Template\Template $this */

use Ffcms\Templex\Url\Url;

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
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
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
// language change flags if enabled
if (\App::$Properties->get('multiLanguage') && count(\App::$Properties->get('languages')) > 1) {
    foreach (\App::$Properties->get('languages') as $lang) {
        $navbar->menu('left', [
            'text' => '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="flag flag-' . $lang . '" alt="' . $lang . '">',
            'link' => App::$Alias->baseUrlNoLang . '/' . $lang . App::$Request->getPathInfo(),
            'html' => true
        ]);
    }
}
if (\App::$User->isAuth()) {
    $userId = \App::$User->identity()->getId();
    $navbar->menu('right', ['text' => __('Account') . ' <span class="badge" id="summary-count-block">0</span>', 'dropdown' => [
        ['text' => __('My profile'), 'link' => ['profile/show', [$userId]]],
        ['text' => __('Messages') . ' <span class="badge bg-secondary" id="pm-count-block">0</span>', 'link' => ['profile/messages'], 'class' => 'dropdown-item', 'html' => true],
        ['text' => __('Feed'), 'link' => ['profile/feed'], 'class' => 'dropdown-item'],
        ['text' => __('Notifications') . ' <span class="badge" id="notify-count-block">0</span>', 'link' => ['profile/notifications'], 'class' => 'dropdown-item', 'html' => true],
        ['text' => __('Settings'), 'link' => ['profile/settings'], 'class' => 'dropdown-item'],
    ], 'properties' => ['html' => true]]);
    if (\App::$User->identity()->role->can('Admin/Main/Index')) {
        $navbar->menu('right', ['text' => __('Admin'), 'link' => \App::$Alias->scriptUrl . '/admin']);
    }
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
        <div class="col-md-4">
            <form class="align-items-center" action="<?= Url::to('search/index') ?>" method="GET">
                <div class="row g-0">
                    <div class="col-8"><input type="text" name="query" class="form-control w-100" id="searchInput" value="<?= isset($query) ? $query : null ?>" placeholder="<?= __('search query...') ?>" autocomplete="off"></div>
                    <div class="col-4 gx-1"><button type="submit" class="btn btn-primary w-100"><?= __('Find') ?></button></div>
                </div>
            </form>
            <div class="searchbox d-none" id="search-popup">
                <div class="list-group" id="search-list">
                    <a href="#" class="list-group-item list-group-item-action">test item</a>
                </div>
            </div>
        </div>
    </div>
</header>

<main role="main" class="container">
    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
    <div class="row">
        <div class="col">
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
        <div class="<?= ($fullgrid ? 'col-md-12' : 'col-md-8') ?>">
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
                    <div class="card-header"><?= __('New content') ?></div>
                    <div class="card-body">
                        <?= \Widgets\Front\Newcontent\Newcontent::widget() ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (\Widgets\Front\Contenttag\Contenttag::enabled()): ?>
                <div class="card mt-1">
                    <div class="card-header"><?= __('Content tags') ?></div>
                    <div class="card-body">
                        <?= \Widgets\Front\Contenttag\Contenttag::widget() ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (Widgets\Front\Newcomment\Newcomment::enabled()): ?>
                <div class="card mt-1">
                    <div class="card-header"><?= __('New comments') ?></div>
                    <div class="card-body">
                        <?= \Widgets\Front\Newcomment\Newcomment::widget() ?>
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
            <p>&copy; <?= date('Y') ?> website. Powered by <a href="https://ffcms.org">ffcms.org</a>.</p>
        </div>
    </div>
</footer>

<?php $this->insert('blocks/cookie-agree') ?>

<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/jquery/dist/jquery.min.js"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= \App::$Alias->currentViewUrl ?>/assets/js/global.js"></script>

<!-- jQuery code interprier after library loaded -->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>

<?= $this->section('javascript') ?>

<?php if (\App::$Debug): ?>
    <?= \App::$Debug->renderOut() ?>
<?php endif; ?>
</body>
</html>