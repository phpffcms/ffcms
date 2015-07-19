<?php
/** @var $body string */
use Ffcms\Core\Helper\HTML\Listing;
use Ffcms\Core\Helper\Type\Object;

?>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="<?php echo \App::$Alias->getVendor('css', 'bootstrap'); ?>"/>
    <link rel="stylesheet" href="<?php echo \App::$Alias->getVendor('css', 'fa'); ?>"/>
    <link rel="stylesheet" href="<?php echo \App::$Alias->currentViewUrl ?>/assets/css/theme.css"/>
    <?php echo \App::$View->showCodeLink('css'); ?>
    <title><?php echo \App::$Security->escapeQuotes($this->title) ?></title>
    <meta name="keywords" content="<?php echo \App::$Security->escapeQuotes($this->keywords); ?>"/>
    <meta name="description" content="<?php echo \App::$Security->escapeQuotes($this->description); ?>"/>
    <?php
    $customCssCode = \App::$View->showPlainCode('css');
    if ($customCssCode !== null) {
        echo '<style>' . $customCssCode . '</style>';
    } ?>
    <script>
        window.jQ = [];
        var script_url = '<?= \App::$Alias->scriptUrl ?>';
        var script_lang = '<?= \App::$Request->getLanguage() ?>';
    </script>
</head>
<body>
<div class="container account-container">
    <div class="text-right">
        <?php
        $accountPanel = [];
        if (\App::$User->isAuth()) {
            $userId = \App::$User->identity()->getId();
            $accountPanel = [
                ['type' => 'link', 'link' => ['profile/show', $userId], 'text' => '<i class="fa fa-user"></i> ' . __('Profile'), 'html' => true],
                ['type' => 'link', 'link' => ['profile/messages'], 'text' => '<i class="fa fa-envelope"> ' . __('Messages') . ' <span class="badge pm-count-block">0</span>', 'html' => true],
                ['type' => 'link', 'link' => ['user/logout'], 'text' => '<i class="fa fa-user-secret"></i> ' . __('Logout'), 'html' => true]
            ];

            if (\App::$User->identity()->getRole()->can('Admin/Main/Index')) {
                $accountPanel[] = ['type' => 'link', 'link' => \App::$Alias->scriptUrl . '/admin/', 'text' => '<i class="fa fa-cogs"></i> Admin', 'html' => true];
            }
        } else {
            $accountPanel = [
                ['type' => 'link', 'link' => ['user/login'], 'text' => '<i class="fa fa-sign-in"></i> ' . __('Sign in'), 'html' => true],
                ['type' => 'link', 'link' => ['user/signup'], 'text' => '<i class="fa fa-check-square-o"></i> ' . __('Sign up'), 'html' => true]
            ];
        }

        echo Listing::display([
            'type' => 'ul',
            'id' => 'account-list',
            'activeOrder' => 'action',
            'property' => ['class' => 'list-inline account-list'],
            'items' => $accountPanel
        ]);

        ?>
    </div>
</div>

<div class="container body-container">

    <!-- head logo and search panel -->
    <div class="row header-block">
        <!-- Image logo -->
        <div class="col-md-1">
            <img alt="Website logo" src="<?php echo \App::$Alias->currentViewUrl; ?>/assets/img/logo.png" class="img-responsive" style="padding-top: 5px;">
        </div>
        <!-- text logo -->
        <div class="col-md-7">
            <div class="site-name"><a href="<?php echo \App::$Alias->baseUrl; ?>">Website title</a></div>
            <p>Some website short description there!</p>
        </div>
        <!-- Search panel -->
        <div class="col-md-4">
            <form method="get" action="http://ffcms.local/ru/search/find/" style="padding-top: 20px;">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="<?php echo __('search query...'); ?>" name="query">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" id="search-submit" type="submit"><?php echo __('Find'); ?></button>
                                </span>
                </div>
            </form>
        </div>
    </div>

    <!-- Main menu -->
    <?php echo \Ffcms\Core\Helper\HTML\Bootstrap\Navbar::display([
        'nav' => ['class' => 'navbar-default'],
        'property' => ['id' => 'headmenu', 'class' => 'navbar-nav'],
        'brand' => ['link' => '/', 'text' => __('Home')],
        'collapseId' => 'collapse-mainmenu',
        'activeOrder' => 'action',
        'items' => [
            ['link' => ['content/news'], 'text' => __('News'), 'position' => 'left'],
            ['link' => ['content/page', 'about.html'], 'text' => __('About'), 'position' => 'left'],
            ['link' => ['feedback/create'], 'text' => __('Feedback'), 'position' => 'left'],
            ['link' => ['profile/index/all'], 'text' => __('Users'), 'position' => 'right']
        ]
    ]); ?>

    <div class="row">
        <div class="col-md-9 content-container">
            <?php if ($this->breadcrumbs !== null && Object::isArray($this->breadcrumbs)) : ?>
            <ol class="breadcrumb">
                <?php foreach ($this->breadcrumbs as $bUrl => $bText): ?>
                    <?php if (Object::isLikeInt($bUrl)): // only text ?>
                    <li class="active"><?= \App::$Security->strip_tags($bText) ?></li>
                    <?php else: ?>
                    <li>
                        <a href="<?= \App::$Security->strip_tags($bUrl) ?>">
                            <?= \App::$Security->strip_tags($bText) ?>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
            <?php endif; ?>
            <?php
            if ($body != null) {
                // display notify if not used in views
                $notify = \App::$Session->getFlashBag()->all();
                if (Object::isArray($notify) && count($notify) > 0) {
                    echo \App::$View->render('macro/notify', ['notify' => $notify]);
                }

                echo $body;
            } else {
                \App::$Response->setStatusCode(404);
                echo '<p>' . __('Page is not founded!') . '</p>';
            }
            ?>
        </div>
        <div class="col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">Title</div>
                <div class="panel-body">
                    Some block body <br />
                    <?php
                    echo Ffcms\Widgets\Ckeditor\Widget::widget();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p>Copyright &copy 2015. Powered on <a href="https://ffcms.org" target="_blank">ffcms</a>.</p>
            </div>
        </div>
    </div>
</footer>
<script src="<?php echo \App::$Alias->getVendor('js', 'jquery'); ?>"></script>
<script src="<?php echo \App::$Alias->getVendor('js', 'bootstrap'); ?>"></script>
<?php echo \App::$View->showCodeLink('js'); ?>
<script>
    $.each(window.jQ, function(index, fn) {
        fn();
    });
</script>
<script>
    $(function(){
        $.getJSON(script_url+'/api/profile/messagesnewcount?lang='+script_lang, function(resp){
            if (resp.status === 1) {
                if (resp.count > 0) {
                    $('.pm-count-block').html(resp.count).addClass('alert-danger', 1000);
                }
            }
        });
    });
</script>
<?php
$customJsCode = \App::$View->showPlainCode('js');
if ($customJsCode !== null) {
    echo '<script>' . $customJsCode . '</script>';
}
?>
</body>
</html>