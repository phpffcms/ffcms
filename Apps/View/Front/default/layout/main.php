<?php
/** @var $body string */
use Apps\ActiveRecord\App as AppRecord;
use Ffcms\Core\Helper\HTML\Bootstrap\Navbar;
use Ffcms\Core\Helper\Type\Obj;
use Widgets\Basic\LanguageSwitcher;

?>
<!DOCTYPE html>
<html lang="<?= \App::$Request->getLanguage() ?>">
<head>
    <meta charset="utf-8" />
    <meta http-equiv=X-UA-Compatible content="IE=edge">
	<meta name=viewport content="width=device-width,initial-scale=1">
    <link rel="shortcut icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?= \App::$Alias->currentViewUrl ?>/assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo \App::$Alias->getVendor('css', 'bootstrap'); ?>"/>
    <title><?php echo \App::$Security->escapeQuotes($this->title) ?></title>
    <meta name="keywords" content="<?php echo \App::$Security->escapeQuotes($this->keywords); ?>"/>
    <meta name="description" content="<?php echo \App::$Security->escapeQuotes($this->description); ?>"/>
    <?php
    $customCssCode = \App::$View->showPlainCode('css');
    if ($customCssCode !== null) {
        echo '<style>' . $customCssCode . '</style>';
    } ?>
    <script>
        var script_url = '<?= \App::$Alias->scriptUrl ?>';
        var script_lang = '<?= \App::$Request->getLanguage() ?>';
        var site_url = '<?= \App::$Alias->baseUrl ?>';
    </script>
    <!-- jquery usage after-load logic -->
    <script>(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y);}else{w.readyQ.push(x);}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script>
</head>
<body>
<header class="container nopadding">
<?php
$items = LanguageSwitcher::widget(['onlyArrayItems' => true]);

if (\App::$User->isAuth()) {
    $userId = \App::$User->identity()->getId();
    // show 'add content' button if current controller is Content and user add is enabled
    if (\App::$Request->getController() === 'Content' && (bool)AppRecord::getConfig('app', 'Content', 'userAdd')) {
        $items[] = ['type' => 'link', 'link' => ['content/update'], 'text' => '<i class="glyphicon glyphicon-plus"></i> ' . __('Add content'), 'html' => true, 'position' => 'right'];
    }
    $accountDropdown[] = ['link' => ['profile/show', $userId], 'text' => __('My profile')];
    $accountDropdown[] = ['link' => ['profile/messages'], 'text' => __('Messages') . ' <span class="badge" id="pm-count-block">0</span>', 'html' => true, '!secure' => true];
    $accountDropdown[] = ['link' => ['profile/notifications'], 'text' => __('Notifications') . ' <span class="badge" id="notify-count-block">0</span>', 'html' => true, '!secure' => true];
    if ((bool)AppRecord::getConfig('app', 'Content', 'userAdd')) {
        $accountDropdown[] = ['link' => ['content/my'], 'text' => __('My content')];
    }
    $accountDropdown[] = ['link' => ['profile/settings'], 'text' => __('Settings')];

    $items[] = [
        'type' => 'dropdown',
        'text' => '<i class="glyphicon glyphicon-user"></i> ' . __('Account') . ' <span class="badge" id="summary-count-block">0</span>',
        'html' => true,
        '!secure' => true,
        'position' => 'right',
        'items' => $accountDropdown
    ];
    if (\App::$User->identity()->getRole()->can('Admin/Main/Index')) {
        $items[] = ['type' => 'link', 'link' => \App::$Alias->scriptUrl . '/admin/', 'text' => '<i class="glyphicon glyphicon-cog"></i> Admin', 'position' => 'right', 'html' => true];
    }
    $items[] = ['type' => 'link', 'link' => ['user/logout'], 'text' => '<i class="glyphicon glyphicon-log-out"></i> ' . __('Logout'), 'html' => true, 'position' => 'right'];
} else {
    $items[] = ['type' => 'link', 'link' => ['user/login'], 'text' => '<i class="glyphicon glyphicon-log-in"></i> ' . __('Sign in'), 'position' => 'right', 'html' => true];
    $items[] = ['type' => 'link', 'link' => ['user/signup'], 'text' => '<i class="glyphicon glyphicon-check"></i> ' . __('Sign up'), 'position' => 'right', 'html' => true];
}

echo Navbar::display([
    'nav' => ['class' => 'navbar-inverse', 'style' => 'padding-left: 0'],
    'property' => ['id' => 'headmenu', 'class' => 'navbar-nav'],
    'activeOrder' => 'action',
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
			<p>This is a test description of new website.</p>
		</div>
		<!-- Search/language panel -->
		<div class="col-md-4">
			<!-- search panel -->
			<form method="get" action="<?= \Ffcms\Core\Helper\Url::to('search/index') ?>" style="padding-top: 20px;">
				<div class="input-group">
					<input id="search-line" type="text" class="form-control" placeholder="<?php echo __('search query...'); ?>" name="query" autocomplete="off" required>
					<span class="input-group-btn">
						<button class="btn btn-default" id="search-submit" type="submit"><?php echo __('Find'); ?></button>
					</span>
				</div>
			</form>
            <div id="ajax-result-container" class="hidden" style="position: fixed;z-index: 9999;">
                <div class="list-group col-md-6 col-xs-12" id="ajax-result-items"></div>
                <div id="ajax-carcase-item" class="hidden">
                    <a href="#" class="list-group-item" id="ajax-search-link">
                        <div class="h4 list-group-item-heading" id="ajax-search-title"></div>
                        <p class="list-group-item-text" id="ajax-search-snippet"></p>
                    </a>
                </div>
            </div>
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
                    <li>
                        <a href="<?= \App::$Security->strip_tags($bUrl) ?>"><?= \App::$Security->strip_tags($bText) ?></a>
                    </li>
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
            <?php if (Widgets\Front\Newcontent\Newcontent::enabled()): ?>
            <div class="panel panel-primary">
				<div class="panel-heading"><?= __('New content') ?></div>
				<div class="panel-body">
                    <?= Widgets\Front\Newcontent\Newcontent::widget(); ?>
                </div>
			</div>
            <?php endif; ?>

            <?php if (Widgets\Front\Contenttag\Contenttag::enabled()): ?>
			<div class="panel panel-primary">
				<div class="panel-heading"><?= __('Content tags') ?></div>
					<div class="panel-body">
                    <?= Widgets\Front\Contenttag\Contenttag::widget() ?>
                </div>
			</div>
            <?php endif; ?>

            <?php if (Widgets\Front\Newcomment\Newcomment::enabled()): ?>
			<div class="panel panel-primary">
				<div class="panel-heading"><?= __('New comments') ?></div>
					<div class="panel-body">
                    <?= Widgets\Front\Newcomment\Newcomment::widget() ?>
                </div>
			</div>
            <?php endif; ?>
		</div>
	</div>
</div>

<!-- Website footer data. Please save us copyright's, it's all what we have ...  -->
<footer>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<p>Copyright &copy; 2015-2016. Powered by <a href="https://ffcms.org" target="_blank">ffcms</a>.</p>
			</div>
		</div>
	</div>
</footer>
<link rel="stylesheet" href="<?php echo \App::$Alias->currentViewUrl ?>/assets/css/theme.min.css"/>
<script src="<?php echo \App::$Alias->getVendor('js', 'jquery'); ?>"></script>
<script defer src="<?php echo \App::$Alias->getVendor('js', 'bootstrap'); ?>"></script>
<script defer src="<?= \App::$Alias->currentViewUrl ?>/assets/js/ffcms.min.js"></script>
<?php echo \App::$View->showCodeLink('css') ?>
<?php echo \App::$View->showCodeLink('js'); ?>
<script>
    $(document).ready(function(){
        // notification function for user pm count block (class="pm-count-block")
        var loadPmInterval = false;
        var summaryBlock = $('#summary-count-block');
        var msgBlock = $('#pm-count-block');
        var notifyBlock = $('#notify-count-block');
        function ajaxNotify() {
            $.getJSON(script_url+'/api/profile/notifications?lang='+script_lang, function(resp){
                if (resp.status === 1) {
                    if (resp.summary > 0) {
                        summaryBlock.addClass('alert-danger', 1000).text(resp.summary);
                        // set new messages count
                        if (resp.messages > 0) {
                            msgBlock.text(resp.messages).addClass('alert-danger', 1000);
                        } else {
                            msgBlock.removeClass('alert-danger', 1000).text(0);
                        }
                        // set new notifications count
                        if (resp.notify > 0) {
                            notifyBlock.text(resp.notify).addClass('alert-danger', 1000);
                        } else {
                            notifyBlock.removeClass('alert-danger', 1000).text(0);
                        }
                    } else {
                        summaryBlock.removeClass('alert-danger', 1000).text(0);
                    }
                    setNotificationNumber(resp.summary);
                } else if (loadPmInterval !== false) { // remove autorefresh
                    clearInterval(loadPmInterval);
                }
            }).fail(function(){
                if (loadPmInterval !== false)
                    clearInterval(loadPmInterval);
            });
        }

        // instantly run counter
        ajaxNotify();
        // make autorefresh every 10 seconds
        loadPmInterval = setInterval(ajaxNotify, 10000);
        // make live search on user keypress in search input
        $('#search-line').keypress(function(e){
            // bind key code
            var keycode = ((typeof e.keyCode !='undefined' && e.keyCode) ? e.keyCode : e.which);
            // bind current complete query from input field
            var query = $(this).val();
            // check if pressed ESC button to hide dropdown results
            if (keycode === 27) {
                $('#ajax-result-container').addClass('hidden');
                return;
            }
            if (query.length < 2)
                return;
            // cleanup & make AJAX query with building response
            $('#ajax-result-items').empty();
            $.getJSON(script_url+'/api/search/index?query='+query+'&lang='+script_lang, function (resp) {
                if (resp.status !== 1 || resp.count < 1)
                    return;
                var searchHtml = $('#ajax-carcase-item').clone().removeClass('hidden');
                $.each(resp.data, function(relevance, item) {
                    var searchItem = searchHtml.clone();
                    searchItem.find('#ajax-search-link').attr('href', '<?= \App::$Alias->baseUrl ?>'+item.uri);
                    searchItem.find('#ajax-search-title').text(item.title);
                    searchItem.find('#ajax-search-snippet').text(item.snippet);
                    $('#ajax-result-items').append(searchItem.html());
                    searchItem = null;
                });
                $('#ajax-result-container').removeClass('hidden');
            });
        });
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
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
</body>
</html>