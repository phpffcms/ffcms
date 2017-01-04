<?php
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

$socialInstance = \App::$User->getOpenidInstance();
?>

<?php if ($socialInstance !== null): ?>
    <link rel="stylesheet" href="<?php echo \App::$Alias->getVendor('css', 'fa'); ?>"/>
    <div class="row" style="padding-bottom: 5px;">
        <div class="col-md-offset-3 col-md-9">
            <?php foreach ($socialInstance->getProviders() as $provider => $connected): ?>
                <a href="<?= Url::to('user/socialauth', $provider) ?>" class="label label-success">
                    <i class="fa fa-<?= Str::lowerCase($provider) ?>"></i> <?= $provider ?>
                </a>&nbsp;
            <?php endforeach; ?>
            <p class="help-block"><?= __('You can login to website using your social network account') ?></p>
        </div>
    </div>
<?php endif; ?>