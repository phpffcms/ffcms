<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FFCMS Admin - main</title>
    <link rel="stylesheet" href="<?php echo \App::$Alias->getVendor('css', 'bootstrap'); ?>"/>
    <link rel="stylesheet" href="<?php echo \App::$Alias->getVendor('css', 'fa'); ?>"/>
    <link href="<?php echo \App::$Alias->currentViewUrl; ?>/assets/css/site.css" rel="stylesheet">
    <link href="<?php echo \App::$Alias->currentViewUrl; ?>/assets/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="<?php echo \App::$Alias->currentViewUrl; ?>/assets/css/sb-admin-2.css" rel="stylesheet">
    <?php echo \App::$View->showCodeLink('css'); ?>
    <?php
    $customCssCode = \App::$View->showPlainCode('css');
    if ($customCssCode !== null) {
        echo '<style>' . $customCssCode . '</style>';
    } ?>
</head>
<body>

<div id="wrapper">

    <!-- Navigation -->
    <nav id="w0" class="navbar navbar-default navbar-static-top" style="margin-bottom: 0" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#w0-collapse"><span
                    class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span></button>
            <a class="navbar-brand" href="<?php echo App::$Alias->baseUrl ?>">FFCMS<sup>3</sup> Dashboard</a></div>
        <div id="w0-collapse" class="collapse navbar-collapse">
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell fa-fw"></i> Fast Access <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Add page</strong>
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-list-alt fa-lg"></i>
                                    </span>
                                </div>
                                <div>Using this function you can add static page to website</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Add page</strong>
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-plus fa-lg"></i>
                                    </span>
                                </div>
                                <div>Using this function you can add news material on website</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Read feedback</strong>
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-envelope fa-lg"></i>
                                    </span>
                                </div>
                                <div>Using this function you can read current feedback from users</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Moderate comments</strong>
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-plus fa-lg"></i>
                                    </span>
                                </div>
                                <div>Using this function you can moderate user comments</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>View All admin notify</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-messages -->
                </li>
                <!-- /.dropdown -->
                <li><a href="<?php echo \App::$Alias->scriptUrl; ?>" target="_blank">
                        <i class="fa fa-arrow-right"></i>
                        View site </a>
                </li>
                <li><a href="/admin/ru/site/logout" data-method="post"><i class="fa fa-sign-out"></i> Sign Out</a></li>
            </ul>

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li><a href="/admin/ru/" class="active">
                                <i class="fa fa-home fa-fw"></i> Главная</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fire fa-fw"></i> Система<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="/admin/ru/site/settings">
                                        <i class="fa fa-cogs"></i> Настройки </a>
                                </li>
                                <li>
                                    <a href="/admin/ru/site/filemanager">
                                        <i class="fa fa-file-o"></i> Менеджер файлов </a>
                                </li>
                                <li>
                                    <a href="/admin/ru/site/antivirus">
                                        <i class="fa fa-shield"></i> Антивирус </a>
                                </li>
                                <li>
                                    <a href="/admin/ru/gii">
                                        <i class="fa fa-code"></i> Генератор кода </a>
                                </li>
                                <li>
                                    <a href="/admin/ru/site/updates">
                                        <i class="fa fa-gavel"></i> Обновления</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-plug fa-fw"></i> Приложения<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="/admin/ru/app/page">
                                        page </a>
                                </li>
                                <li>
                                    <a href="/admin/ru/app/user">
                                        user </a>
                                </li>
                                <li>
                                    <a href="/admin/ru/app/news">
                                        news </a>
                                </li>
                                <li>
                                    <a href="/admin/ru/app/feedback">
                                        feedback </a>
                                </li>
                                <li><a href="#">Просмотреть все</a></li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-table fa-fw"></i> Виджеты<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="#">Просмотреть все</a></li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>

        </div>
    </nav>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="site-index">
                    <?php echo $body; ?>
                </div>
            </div>
            <!-- /.col-md-12 -->
        </div>
        <!-- /.row -->
        <div class="row" style="padding-top: 20px;">
            <p class="text-center"><a href="#" onclick="window.history.back();return false;"><span
                        class="label label-primary"><i class="fa fa-arrow-left"></i> Назад</span></a></p>
        </div>
    </div>
    <!-- /#page-wrapper -->

</div>
<script src="<?php echo \App::$Alias->getVendor('js', 'jquery'); ?>"></script>
<script src="<?php echo \App::$Alias->getVendor('js', 'bootstrap'); ?>"></script>
<script src="<?php echo \App::$Alias->currentViewUrl; ?>/assets/js/metisMenu.min.js"></script>
<script src="<?php echo \App::$Alias->currentViewUrl; ?>/assets/js/sb-admin-2.js"></script>
<?php echo \App::$View->showCodeLink('js'); ?>
<?php
$customJsCode = \App::$View->showPlainCode('js');
if ($customJsCode !== null) {
    echo '<script>' . $customJsCode . '</script>';
}
?>
<?php echo \App::$Debug->render->render() ?>
</body>
</html>