<?php
/** @var $body string */
use Ffcms\Core\Helper\Type\Obj;
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv=X-UA-Compatible content="IE=edge">
	<meta name=viewport content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex, nofollow" />
    <meta name="robots" content="noarchive"/>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="<?php echo \App::$Alias->getVendor('css', 'bootstrap'); ?>"/>
    <link rel="stylesheet" href="<?php echo \App::$Alias->getVendor('css', 'fa'); ?>"/>
    <link rel="stylesheet" href="<?php echo \App::$Alias->currentViewUrl ?>/assets/css/theme.css"/>
    <?php echo \App::$View->showCodeLink('css'); ?>
    <title><?= __('FFCMS installer') ?></title>
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
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <img src="<?= \App::$Alias->currentViewUrl ?>/assets/img/logo.png" alt="logo" class="img-responsive" />
            </div>
            <div class="col-md-10">
                <h1>FFCMS 3</h1>
                <small><?= __('Fast, flexibility content management system with MVC framework inside!') ?></small>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 body-container">
                <div class="pull-right">
                    <?= \Widgets\Basic\LanguageSwitcher::widget() ?>
                </div>
                <?php
                $notify = \App::$Session->getFlashBag()->all();
                if (Obj::isArray($notify) && count($notify) > 0) {
                    echo \App::$View->render('native/macro/notify', ['notify' => $notify]);
                }
                echo $body;
                ?>
            </div>
        </div>
    </div>
    <script src="<?php echo \App::$Alias->getVendor('js', 'jquery'); ?>"></script>
    <script src="<?php echo \App::$Alias->getVendor('js', 'bootstrap'); ?>"></script>
    <?php echo \App::$View->showCodeLink('js'); ?>
    <script>
        $.each(window.jQ, function(index, fn) {
            fn();
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
