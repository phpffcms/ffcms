<?php
    use Core\Helper\Url;
?>
<html>
<head>
    <link rel="stylesheet" href="<?php echo \App::$Alias->vendor['css']['bootstrap']['url']; ?>"/>
    <link rel="stylesheet" href="<?php echo \App::$Alias->vendor['css']['fa']['url']; ?>"/>
    <link rel="stylesheet" href="<?php echo \App::$Alias->currentViewUrl ?>/assets/css/theme.css"/>
    <title><?php echo \App::$Security->escapeQuotes($global->title) ?></title>
    <meta name="keywords" content="<?php echo \App::$Security->escapeQuotes($global->keywords); ?>"/>
    <meta name="description" content="<?php echo \App::$Security->escapeQuotes($global->description); ?>"/>
    <?php echo \App::$Debug->render->renderHead() ?>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo Url::to('/') ?>">Главная</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><?php echo Url::link(Url::to('news/index'), 'Новости'); ?></li>
                <li><?php echo Url::link(Url::to('page/read', 'about'), 'О сайте'); ?></li>
                <li><a href="<?php echo \App::$Alias->baseUrl; ?>user"> Пользователи</a></li>
                <li><a href="<?php echo \App::$Alias->baseUrl; ?>feedback/">Обратная связь</a></li>
                <!--<li><a href="http://demo.ffcms.ru/en/"><img class="flag flag-en"
                                                            src="http://demo.ffcms.ru/resource/flags/blank.gif"/></a>
                </li>
                <li><a href="http://demo.ffcms.ru/ru/"><img class="flag flag-ru"
                                                            src="http://demo.ffcms.ru/resource/flags/blank.gif"/></a>
                </li>-->
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo \App::$Alias->baseUrl; ?>user/register.html">Регистрация</a></li>
                <li><a href="<?php echo \App::$Alias->baseUrl; ?>user/login.html">Войти</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <a href="<?php echo \App::$Alias->baseUrl; ?>"><img src="<?php echo \App::$Alias->currentViewUrl; ?>/assets/img/logo.png"/></a>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <form id="search">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-term" placeholder="query...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" id="search-submit" type="submit">Найти</button>
                                </span>
                        </div>
                    </form>
                    <script>
                        $(function () {
                            $('#search-submit').click(function (e) {
                                var input_text = $('#search-term').val();
                                if (input_text.length > 0) {
                                    window.location.replace("http://demo.ffcms.ru/ru/search/" + input_text);
                                }
                                return false;
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <div class="row container-content">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>Навигация</h4>
                    <ul class="side-links" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">
                        <li><a href="http://demo.ffcms.ru/ru/" itemprop="url"><span itemprop="name">Главная</span></a>
                        </li>
                        <li><a href="http://demo.ffcms.ru/ru/static/about.html" itemprop="url"><span itemprop="name">О сайте</span></a>
                        </li>
                        <li><a href="http://ffcms.ru" itemprop="url"><span itemprop="name">Проект FFCMS</span></a><span
                                class="toggle-children"></span>
                            <ul>
                                <li><a href="http://ffcms.ru/en/forum/" itemprop="url"><span
                                            itemprop="name">Форум</span></a></li>
                            </ul>
                        </li>
                        <li><a href="http://demo.ffcms.ru/ru/news/" itemprop="url"><span
                                    itemprop="name">Новости сайта</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>Последние комментарии</h4>
                    <a href="http://demo.ffcms.ru/ru/user/id1">admin</a>
                    <i class="fa fa-pencil"></i>
                    &laquo;<a href="http://demo.ffcms.ru/ru/news/demo-ffcms.html#comment_list">Demo comment </a>&raquo;,
                    12.08.2014
                    <hr class="commenttype"/>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4 class="centered">Облако тегов</h4>

                    <p>
                        <a href="http://demo.ffcms.ru/ru/news/tag/ fast.html" class="label label-default"
                           title="Совпадений: 1"> fast</a>
                        <a href="http://demo.ffcms.ru/ru/news/tag/ffcms.html" class="label label-default"
                           title="Совпадений: 1">ffcms</a>
                        <a href="http://demo.ffcms.ru/ru/news/tag/ flexibility.html" class="label label-default"
                           title="Совпадений: 1"> flexibility</a>
                        <a href="http://demo.ffcms.ru/ru/news/tag/ cms.html" class="label label-default"
                           title="Совпадений: 1"> cms</a>
                        <a href="http://demo.ffcms.ru/ru/news/tag/ free.html" class="label label-default"
                           title="Совпадений: 1"> free</a>
                        <a href="http://demo.ffcms.ru/ru/news/tag/ php.html" class="label label-default"
                           title="Совпадений: 1"> php</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php echo $body; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
                <p>Copyright FFCMS тест &copy; 2015. Powered by <a href="http://ffcms.ru" target="_blank">ffcms.ru</a></p>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo \App::$Alias->vendor['js']['jquery']['url']; ?>"></script>
<script src="<?php echo \App::$Alias->vendor['js']['bootstrap']['url']; ?>"></script>
<?php echo \App::$Debug->render->render() ?>
</body>
</html>