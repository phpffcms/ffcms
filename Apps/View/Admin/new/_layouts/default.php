<?php


use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */

// load layout features model
$features = new \Apps\Model\Admin\LayoutFeatures\LayoutFeatures();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= $title ?? 'Admin panel' ?></title>

    <link rel="shortcut icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">

    <link href="<?= \App::$Alias->currentViewUrl ?>/assets/css/theme.css" rel="stylesheet" />
    <link href="<?= \App::$Alias->currentViewUrl ?>/assets/css/style.css" rel="stylesheet" />

    <?php if (\App::$Properties->get('multiLanguage') && count(\App::$Properties->get('languages')) > 1) : ?>
        <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/language-flags/flags.css" />
    <?php endif; ?>

    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css" />

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

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="<?= Url::to('/') ?>">FFCMS Admin</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        <?php if (\App::$Properties->get('multiLanguage') && count(\App::$Properties->get('languages')) > 1) {
            $list = $this->listing('ul', ['class' => 'navbar-nav ml-4']);
            foreach (\App::$Properties->get('languages') as $lang) {
                $list->li([
                    'text' => '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="flag flag-' . $lang . '" alt="' . $lang . '">',
                    'link' => App::$Alias->baseUrlNoLang . '/' . $lang . App::$Request->getPathInfo(),
                    'html' => true
                ], ['class' => 'list-inline-item']);
            }
            echo $list->display();
        } ?>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0" method="get" action="<?= Url::link(['main/search']) ?>">
            <div class="input-group">
                <input name="search" class="form-control" type="text" placeholder="<?= __('Type search query') ?>" aria-label="Search" aria-describedby="basic-addon2" value="<?= $query ?? null ?>" />
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
        <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item list-inline-item">
                <a class="nav-link" href="<?= \App::$Alias->scriptUrl ?>" target="_blank"><i class="fas fa-globe-europe"></i></a>
            </li>
        </ul>
        <!-- Navbar-->
        <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="<?= Url::to('user/update', [\App::$User->identity()->id]) ?>"><?= __('Settings') ?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= \App::$Alias->scriptUrl ?>/user/logout" data-method="post"><?= __('Logout') ?></a>
                </div>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading"><?= __('Core') ?></div>
                        <a class="nav-link" href="<?= Url::to('/') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            <?= __('Dashboard') ?>
                        </a>
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            <?= __('Settings') ?>
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseSettings" aria-labelledby="areaCollapseSettings" data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <?= Url::a(['main/settings'], '<i class="fas fa-cogs pr-2"></i> ' . __('Settings'), ['class' => 'nav-link', 'html' => true]) ?>
                                <?= Url::a(['main/files'], '<i class="fas fa-file pr-2"></i> ' . __('Files'), ['class' => 'nav-link', 'html' => true]) ?>
                                <?= Url::a(['main/antivirus'], '<i class="fas fa-shield-alt pr-2"></i> ' . __('Antivirus'), ['class' => 'nav-link', 'html' => true]) ?>
                                <?= Url::a(['main/spam'], '<i class="fas fa-robot pr-2"></i> ' . __('Spam'), ['class' => 'nav-link', 'html' => true]) ?>
                                <?= Url::a(['main/routing'], '<i class="fas fa-code pr-2"></i> ' . __('Routing'), ['class' => 'nav-link', 'html' => true]) ?>
                                <?= Url::a(['main/updates'], '<i class="fas fa-gavel pr-2"></i> ' . __('Updates'), ['class' => 'nav-link', 'html' => true]) ?>
                            </nav>
                        </div>
                        <div class="sb-sidenav-menu-heading"><?= __('Features') ?></div>
                        <?php
                        $apps = [];
                        $widgets = [];
                        foreach (\Apps\ActiveRecord\App::all() as $ext) {
                            /** @var \Apps\ActiveRecord\App $ext */
                            if ($ext->type === 'app') {
                                $apps[$ext->sys_name] = $ext;
                            } elseif ($ext->type === 'widget') {
                                $widgets[$ext->sys_name] = $ext;
                            }
                        }
                        ?>
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseApps" aria-expanded="false" aria-controls="collapseApps">
                            <div class="sb-nav-link-icon"><i class="fas fa-plug"></i></div>
                            <?= __('Applications') ?>
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseApps" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <?php 
                                echo Url::a(['application/index'], __('All apps'), ['class' => 'nav-link text-primary']);
                                foreach ($apps as $app) {
                                    /** @var \Apps\ActiveRecord\App $app */
                                    echo Url::a([Str::lowerCase($app->sys_name) . '/index'], $app->getLocaleName(), ['class' => 'nav-link']);
                                }
                                ?>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseWidget" aria-expanded="false" aria-controls="collapseWidget">
                            <div class="sb-nav-link-icon"><i class="fas fa-puzzle-piece"></i></div>
                            <?= __('Widgets') ?>
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseWidget" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <?php 
                                echo Url::a(['widget/index'], __('All widgets'), ['class' => 'nav-link text-primary']);
                                foreach ($widgets as $widget) {
                                    /** @var \Apps\ActiveRecord\App $widget */
                                    echo Url::a([Str::lowerCase($widget->sys_name) . '/index'], $widget->getLocaleName(), ['class' => 'nav-link']);
                                }
                                ?>
                            </nav>
                        </div>


                        <div class="sb-sidenav-menu-heading"><?= __('Other') ?></div>
                        <a class="nav-link" href="charts.html">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                            <?= __('Market') ?>
                        </a>

                    </div>
                </div>
                <div class="sb-sidenav-footer pb-4">
                    <div class="small">Logged in as:</div>
                    <?= \App::$User->identity()->email ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <div class="row pt-4">
                        <div class="col">
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
                                echo $this->bootstrap()->alert('warning', __('Page not found!'));
                            }
                            ?>
                        </div>
                    </div>

                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; FFCMS 2015 - <?= date('Y') ?></div>
                        <div>
                            <a href="<?= \App::$Alias->scriptUrl ?>/LICENSE">License</a>
                            &middot;
                            <a href="https://pmcore.ru">pmcore.ru</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

    <script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

    <script src="<?= \App::$Alias->currentViewUrl ?>/assets/js/scripts.js"></script>

    <?php if (\App::$Debug) : ?>
        <?= \App::$Debug->renderOut() ?>
    <?php endif; ?>

    <?= $this->section('javascript') ?>

    <!-- jQuery code interprier after library loaded -->
    <script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
</body>

</html>