<?php
/** @var $body string */
use Ffcms\Core\Helper\Type\Obj;
use Widgets\Basic\LanguageSwitcher;
use Ffcms\Core\Arch\Widget;
use Ffcms\Core\Helper\HTML\Bootstrap\Navbar;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv=X-UA-Compatible content="IE=edge">
	<meta name=viewport content="width=device-width,initial-scale=1">
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
        var site_url = '<?= \App::$Alias->baseUrl ?>';
    </script>
</head>
<body>
<header class="container nopadding">
<?php
$items = LanguageSwitcher::widget(['onlyArrayItems' => true]);

if (\App::$User->isAuth()) {
    $userId = \App::$User->identity()->getId();
    $items[] = ['type' => 'dropdown', 'text' => '<i class="fa fa-user"></i> ' . __('Account') . ' <span class="badge pm-count-block">0</span>', 'html' => true, 'position' => 'right', 'items' => [
        ['link' => ['profile/show', $userId], 'text' => __('My profile')],
        ['link' => ['profile/messages'], 'text' => __('Messages') . ' <span class="badge pm-count-block">0</span>', 'html' => true],
        ['link' => ['profile/settings'], 'text' => __('Settings')]
    ]];
    if (\App::$User->identity()->getRole()->can('Admin/Main/Index')) {
        $items[] = ['type' => 'link', 'link' => \App::$Alias->scriptUrl . '/admin/', 'text' => '<i class="fa fa-cogs"></i> Admin', 'position' => 'right', 'html' => true];
    }
    $items[] = ['type' => 'link', 'link' => ['user/logout'], 'text' => '<i class="fa fa-user-times"></i> ' . __('Logout'), 'html' => true, 'position' => 'right'];
} else {
    $items[] = ['type' => 'link', 'link' => ['user/login'], 'text' => '<i class="fa fa-sign-in"></i> ' . __('Sign in'), 'position' => 'right', 'html' => true];
    $items[] = ['type' => 'link', 'link' => ['user/signup'], 'text' => '<i class="fa fa-check-square-o"></i> ' . __('Sign up'), 'position' => 'right', 'html' => true];
}

echo Navbar::display([
    'nav' => ['class' => 'navbar-inverse', 'style' => 'padding-left: 0'],
    'property' => ['id' => 'headmenu', 'class' => 'navbar-nav'],
    'container' => 'container',
    'collapseId' => 'collapse-object',
    'items' => $items
]);?>
</header>

<div class="container body-container">

	<!-- head logo and search panel -->
	<div class="row header-block">
		<!-- Image logo -->
		<div class="col-md-1 hidden-sm hidden-xs col-xs-0">
			<img alt="Website logo" src="<?php echo \App::$Alias->currentViewUrl; ?>/assets/img/logo.png" class="img-responsive" style="padding-top: 5px;">
		</div>
		<!-- text logo -->
		<div class="col-md-7">
			<div class="site-name">
                <?= \Ffcms\Core\Helper\Url::link(['/'], 'FFCMS Demo'); ?>
            </div>
			<p>Some website short description there!</p>
		</div>
		<!-- Search/language panel -->
		<div class="col-md-4">
			<!-- search panel -->
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
    <?= Navbar::display([
        'nav' => ['class' => 'navbar-default'],
        'property' => ['id' => 'headmenu', 'class' => 'navbar-nav'],
        'brand' => ['link' => '/', 'text' => __('Home')],
        'collapseId' => 'collapse-mainmenu',
        'activeOrder' => 'action',
        'items' => [
            ['link' => ['content/list', 'news'], 'text' => __('News'), 'position' => 'left'],
            ['link' => ['content/read', 'page', 'about-page'], 'text' => __('About'), 'position' => 'left'],
            ['link' => ['feedback/create'], 'text' => __('Feedback'), 'position' => 'left'],
            ['link' => ['profile/index/all'], 'text' => __('Users'), 'position' => 'right']
        ]
    ]);
    ?>

    <div class="row">
		<div class="col-md-9 content-container">
            <?php if ($this->breadcrumbs !== null && Obj::isArray($this->breadcrumbs)) : ?>
            <ol class="breadcrumb">
                <?php foreach ($this->breadcrumbs as $bUrl => $bText): ?>
                    <?php if (Obj::isLikeInt($bUrl)): // only text ?>
                    <li class="active"><?= \App::$Security->strip_tags($bText) ?></li>
                    <?php else: ?>
                    <li><a
						href="<?= \App::$Security->strip_tags($bUrl) ?>">
                            <?= \App::$Security->strip_tags($bText) ?>
                        </a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
            <?php endif; ?>
            <?php
            if ($body != null) {
                // display notify if not used in views
                $notify = \App::$Session->getFlashBag()->all();
                if (Obj::isArray($notify) && count($notify) > 0) {
                    echo \App::$View->render('native/macro/notify', ['notify' => $notify]);
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
				<div class="panel-heading"><?= __('New content') ?></div>
				<div class="panel-body">
                    <?= Widgets\Front\Newcontent\Newcontent::widget(); ?>
                </div>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading"><?= __('Content tags') ?></div>
					<div class="panel-body">
                    <?= Widgets\Front\Contenttag\Contenttag::widget() ?>
                </div>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading"><?= __('New comments') ?></div>
					<div class="panel-body">
                    <?= Widgets\Front\Newcomment\Newcomment::widget() ?>
                </div>
			</div>
		</div>
	</div>
</div>


<footer>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<p>Copyright &copy; 2015. Powered on <a href="https://ffcms.org" target="_blank">ffcms</a>.</p>
			</div>
		</div>
	</div>
</footer>
<script src="<?php echo \App::$Alias->getVendor('js', 'jquery'); ?>"></script>
<script src="<?php echo \App::$Alias->getVendor('js', 'bootstrap'); ?>"></script>
<script src="<?= \App::$Alias->currentViewUrl ?>/assets/js/basic.js"></script>
<?php echo \App::$View->showCodeLink('js'); ?>
<script>
    $.each(window.jQ, function(index, fn) {
        fn();
    });
</script>
	<script>
    // notification function for user pm count block (class="pm-count-block")
    var loadPmInterval = false;
    function ajaxNotifyPm() {
        $.getJSON(script_url+'/api/profile/messagesnewcount?lang='+script_lang, function(resp){
            if (resp.status === 1) {
                var block = $('.pm-count-block');
                if (resp.count > 0) {
                    block.html(resp.count).addClass('alert-danger', 1000);
                } else {
                    block.removeClass('alert-danger', 1000).html(resp.count);
                }
                setNotificationNumber(resp.count);
            } else if (loadPmInterval !== false) { // remove autorefresh
                clearInterval(loadPmInterval);
            }
        });
    }
    $(function(){
        // instantly run counter
        ajaxNotifyPm();
        // make autorefresh every 5 seconds
        loadPmInterval = setInterval('ajaxNotifyPm()', 5000);
    });
</script>
<?php
$customJsCode = \App::$View->showPlainCode('js');
if ($customJsCode !== null) {
    echo '<script>' . $customJsCode . '</script>';
}
// render google analytics code here
echo \App::$View->render('blocks/googleanalytics');
?>
</body>
</html>