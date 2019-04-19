<?php


use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */

// load layout features model
$features = new \Apps\Model\Admin\LayoutFeatures\LayoutFeatures();
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/@fortawesome/fontawesome-free/css/all.min.css" />

    <!-- theme -->
    <link rel="stylesheet" href="<?= \App::$Alias->currentViewUrl ?>/assets/css/style.css" />

    <?php if (\App::$Properties->get('multiLanguage') && count(\App::$Properties->get('languages')) > 1): ?>
        <link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/language-flags/flags.css" />
    <?php endif; ?>

    <title><?= $title ?? 'Admin panel' ?></title>

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
<body>

<div id="wrapper">

    <nav class="navbar navbar-admin navbar-toggleable">
        <div class="container-fluid">
            <button type="button" class="sidebar-open d-md-none">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <h1 class="navbar-brand d-none d-md-inline-block">
                <a href="<?= \App::$Alias->baseUrl ?>"><i class="fas fa-globe-europe" aria-hidden="true"></i></a>
                <small style="font-size: 24px;">FFCMS</small>
            </h1>
            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item dropdown active">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cog"></i> <?= __('Quick manage') ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?= Url::a(['main/settings'], __('Settings'), ['class' => 'dropdown-item']) ?></li>
                        <li><?= Url::a(['user/index'], __('Users'), ['class' => 'dropdown-item']) ?></li>
                        <li><?= Url::a(['content/index'], __('Content'), ['class' => 'dropdown-item']) ?></li>
                        <li role="separator" class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= \App::$Alias->scriptUrl ?>/user/logout" data-method="post"><?= __('Logout') ?></a></li>
                    </ul>
                </li>

                <!-- @todo: implement orders when CRM cart shop ready
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cart-arrow-down"></i> Orders
                    </a>
                    <div class="dropdown-menu dropdown-md">
                        <div class="media-items">
                            <div class="media">
                                <div class="media-left">
                                    <a href="#">
                                        <i class="fa fa-shopping-cart fa-2x text-primary"></i>
                                    </a>
                                </div>
                                <div class="media-body text-muted">
                                    <p class="media-heading"><a href="#">4 items, $215.45</a></p>
                                    <span class="text-sm">
                                        <span class="badge badge-secondary">item #1</span>
                                        <span class="badge badge-secondary">item #2</span>
                                        <span class="badge badge-secondary">item #3</span>
                                        <span class="badge badge-secondary">item #4</span>
                                    </span>
                                </div>
                            </div>
                            <div class="media">
                                <div class="media-left">
                                    <a href="#">
                                        <i class="fa fa-shopping-cart fa-2x text-muted"></i>
                                    </a>
                                </div>
                                <div class="media-body text-muted">
                                    <p class="media-heading"><a href="#">1 items, $15.45</a></p>
                                    <span class="text-sm">
                                        <span class="badge badge-secondary">Black dildo 7"</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <a class="dropdown-menu-footer" href="#">
                            View all
                        </a>

                    </div>
                </li> -->

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-question-circle"></i> <?= __('Feedback') ?>
                        <?php if ($features->getFeedback()->count() > 0): ?>
                        <span class="badge badge-primary"><?= $features->getFeedback()->count() ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-md">
                        <div class="media-items">
                            <?php if ($features->getFeedback()->count() < 1):?>
                            <div class="media">
                                <div class="media-body text-muted">
                                    <span class="text-sm">No feedback queries found</span>
                                </div>
                            </div>
                            <?php else: ?>
                                <?php foreach ($features->getFeedback() as $feed): ?>
                                    <div class="media">
                                        <div class="media-left">
                                            <?= Url::a(['feedback/read', [$feed->id]], '<i class="fas fa-question-circle fa-2x text-primary"></i>', ['html' => true]) ?>
                                        </div>
                                        <div class="media-body text-muted">
                                            <p class="media-heading"><?= Url::a(['feedback/read', [$feed->id]], $feed->name) ?></p>
                                            <span class="text-sm"><?= Text::snippet($feed->message, 100) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <a class="dropdown-menu-footer" href="<?= Url::to('feedback/index') ?>">
                            <?= __('View all') ?>
                        </a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-comment-o"></i> <?= __('Comments') ?>
                    </a>
                    <div class="dropdown-menu dropdown-md">
                        <div class="media-items">
                            <?php if ($features->getComments()->count() < 1): ?>
                            <div class="media">
                                <div class="media-body text-muted">
                                    <span class="text-sm">No comments found</span>
                                </div>
                            </div>
                            <?php else: ?>
                                <?php foreach ($features->getComments() as $comment): ?>
                                <?php /** @var \Apps\ActiveRecord\CommentPost $comment */ ?>
                                    <div class="media">
                                        <div class="media-left">
                                            <a href="<?= ($comment->user ? Url::link(['user/update', [$comment->user->id]]) : '#') ?>">
                                                <?php
                                                $commentAva = \App::$Alias->scriptUrl . '/upload/user/avatar/small/default.jpg';
                                                if ($comment->user && $comment->user->id > 0) {
                                                    $commentAva = $comment->user->profile->getAvatarUrl('small');
                                                }
                                                ?>
                                                <img class="media-object img-circle" src="<?= $commentAva ?>" width="38" height="38" alt="avatar" />
                                            </a>
                                        </div>
                                        <div class="media-body text-muted">
                                            <p class="media-heading">
                                                <a href="#"><?= $comment->user_id > 0 ? Simplify::parseUserNick($comment->user_id) : $comment->guest_name ?></a>
                                            </p>
                                            <span class="text-sm"><?= Text::snippet($comment->message, 100) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <a class="dropdown-menu-footer" href="<?= Url::to('comments/index') ?>">
                            <?= __('View all') ?>
                        </a>

                    </div>
                </li>

                <li class="nav-item">
                    <a href="<?= \App::$Alias->scriptUrl ?>" class="nav-link" target="_blank">
                        <i class="fas fa-sign-out"></i> <?= __('Open site') ?>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="content">
        <header id="page-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-8 page-title-wrapper">
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
                        <!--<h1 class="page-title">FFCMS<sup>3</sup></h1>-->
                        <h2 class="page-subtitle">
                        <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                            <?php foreach ($breadcrumbs as $url => $text): ?>
                                <?php if (\Ffcms\Core\Helper\Type\Any::isInt($url)): ?>
                                    <?= $text ?>
                                <?php else: ?>
                                    <a href="<?= $url ?>"><?= $text ?></a> /
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </h2>
                    </div>
                    <div class="col-sm-4 d-none d-md-inline-block page-search-wrapper">
                        <form class="form-inline" method="get" action="<?= Url::link(['main/search']) ?>">
                            <input type="text" class="form-control col" name="search" placeholder="<?= __('Type search query') ?>" value="<?= $query ?? null ?>">&nbsp;
                            <input type="submit" name="submit" value="<?= __('Search') ?>" class="btn btn-secondary" />
                        </form>
                    </div>
                </div>
            </div>
        </header>
        <div id="page-body">
            <!-- left menu -->
            <div class="container-fluid">
                <div id="page-sidebar" class="toggled sidebar">
                    <nav class="sidebar-collapse d-none d-md-block">
                        <i class="fas fa-arrow-right show-on-collapsed"></i>
                        <i class="fas fa-arrow-left hide-on-collapsed"></i>
                    </nav>

                    <ul class="nav nav-pills nav-stacked" id="sidebar-stacked">
                        <li class="d-md-none">
                            <a href="#" class="sidebar-close"><i class="fas fa-arrow-left"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= App::$Alias->baseUrl ?>"><i class="fas fa-home"></i> <span class="nav-text"><?= __('Main') ?></span></a>
                        </li>
                        <li class="nav-item<?= (\App::$Request->getController() === 'Main' && \App::$Request->getAction() !== 'Index') ? ' active' : null ?>">
                            <?= Url::a(['#system-dropdown'],
                                '<i class="fas fa-fire"></i> <span class="nav-text">' . __('System') . '</span>',
                                [
                                    'class' => 'nav-container',
                                    'data-toggle' => 'collapse',
                                    'html' => true
                                ])
                            ?>

                            <?= $this->bootstrap()->nav('ul', ['class' => 'nav nav-pills nav-stacked collapse' . ((\App::$Request->getController() === 'Main' && \App::$Request->getAction() !== 'Index') ? 'in show' : null), 'id' => 'system-dropdown'])
                                ->menu(['link' => ['main/settings'], 'text' => '<i class="fas fa-cogs"></i> ' . __('Settings'), 'html' => true])
                                ->menu(['link' => ['main/files'], 'text' => '<i class="fas fa-file"></i> ' . __('Files'), 'html' => true])
                                ->menu(['link' => ['main/antivirus'], 'text' => '<i class="fas fa-shield-alt"></i> ' . __('Antivirus'), 'html' => true])
                                ->menu(['link' => ['main/spam'], 'text' => '<i class="fas fa-robot"></i> ' . __('Spam'), 'html' => true])
                                ->menu(['link' => ['main/routing'], 'text' => '<i class="fas fa-code"></i> ' . __('Routing'), 'html' => true])
                                ->menu(['link' => ['main/updates'], 'text' => '<i class="fas fa-gavel"></i> ' . __('Updates'), 'html' => true])
                                ->display();
                            ?>
                        </li>
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
                        <li class="nav-item<?= (array_key_exists(\App::$Request->getController(), $apps) || \App::$Request->getController() === 'Application') ? ' active' : null ?>">
                            <?= Url::a(['#apps-dropdown'],
                                '<i class="fas fa-plug"></i> <span class="nav-text">' . __('Applications') . '</span>',
                                [
                                    'class' => 'nav-container',
                                    'data-toggle' => 'collapse',
                                    'html' => true
                                ])
                            ?>

                            <?php
                            $appMenu = $this->bootstrap()->nav('ul', ['class' => 'nav nav-pills nav-stacked collapse' . ((array_key_exists(\App::$Request->getController(), $apps) || \App::$Request->getController() === 'Application') ? 'in show' : null), 'id' => 'apps-dropdown']);
                            foreach ($apps as $app) {
                                /** @var \Apps\ActiveRecord\App $app */
                                $appMenu->menu(['link' => [Str::lowerCase($app->sys_name) . '/index'], 'text' => $app->getLocaleName()]);
                            }
                            $appMenu->menu(['link' => 'application/index', 'text' => __('All apps') . '...']);
                            echo $appMenu->display();
                            ?>
                        </li>
                        <li class="nav-item<?= (array_key_exists(\App::$Request->getController(), $widgets) || \App::$Request->getController() === 'Widget') ? ' active' : null ?>">
                            <?= Url::a(['#widgets-dropdown'],
                                '<i class="fas fa-puzzle-piece"></i> <span class="nav-text">' . __('Widgets') . '</span>',
                                [
                                    'class' => 'nav-container',
                                    'data-toggle' => 'collapse',
                                    'html' => true
                                ])
                            ?>

                            <?php
                            $widgetMenu = $this->bootstrap()->nav('ul', ['class' => 'nav nav-pills nav-stacked collapse' . ((array_key_exists(\App::$Request->getController(), $widgets) || \App::$Request->getController() === 'Widget') ? 'in show' : null), 'id' => 'widgets-dropdown']);
                            foreach ($widgets as $widget) {
                                /** @var \Apps\ActiveRecord\App $widget */
                                $widgetMenu->menu(['link' => [Str::lowerCase($widget->sys_name) . '/index'], 'text' => $widget->getLocaleName()]);
                            }
                            $widgetMenu->menu(['link' => 'widget/index', 'text' => __('All widgets') . '...']);
                            echo $widgetMenu->display();
                            ?>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= Url::to('store/index') ?>"><i class="fas fa-briefcase"></i> <span class="nav-text"><?= __('App store') ?></span></a>
                        </li>
                    </ul>
                </div>
                <!-- / left menu -->
                <div id="page-content">
                    <div class="row page-canvas">
                        <div class="col-md-12">
                            <div class="card card-default widget animated" style="animation-delay: 0.05s;">
                                <div class="card-body content-body">
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
                    </div>

                    <footer id="footer" class="text-center">
                        <p>All rights reserved &copy; <a href="https://ffcms.org">FFCMS</a>, 2016 - <?= date('Y') ?></p>
                        <ul class="list-inline">
                            <li class="list-inline-item"><a href="https://ffcms.org">Project</a></li>
                            <li class="list-inline-item"><a href="https://github.com/phpffcms">Github</a></li>
                            <li class="list-inline-item"><a href="https://doc.ffcms.org">Docs</a></li>
                            <li class="list-inline-item"><a href="https://ffcms.org/feedback/create">Contact</a></li>
                        </ul>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /#wrapper -->

<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/jquery/dist/jquery.min.js"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/popper.js/dist/umd/popper.min.js"></script>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

<?php if (\App::$Debug): ?>
    <?= \App::$Debug->renderOut() ?>
<?php endif; ?>

<?= $this->section('javascript') ?>

<!-- jQuery code interprier after library loaded -->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>

<script>
    $(document).ready(function(){
        $('.sidebar-collapse').on('click', function(){
            $('#page-body').toggleClass('collapsed');
        });
        $('.sidebar-open').on('click', function(){
            $('#page-sidebar').removeClass('toggled');
        });
        $('.sidebar-close').on('click', function(){
            $('#page-sidebar').addClass('toggled');
        });

        $('.nav-stacked').on('show.bs.collapse', function () {
            $('.nav-stacked .in').collapse('hide');
        });

        $('[data-toggle="tooltip"]').tooltip()
    });
</script>
</body>
</html>