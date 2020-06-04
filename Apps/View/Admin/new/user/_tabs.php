<?php
/** @var \Ffcms\Templex\Template\Template $this */

$configs = \Apps\ActiveRecord\App::getConfigs('app', 'User');
?>

<?php
$menu = $this->bootstrap()->nav('ul', ['class' => 'nav-tabs nav-fill'])
    ->menu(['text' => __('User list'), 'link' => ['user/index']]);
if ((int)$configs['registrationType'] === 0) {
    $menu->menu(['text' => __('Invitation list'), 'link' => ['user/invitelist']]);
}
$menu->menu(['text' => __('Role management'), 'link' => ['user/rolelist']])
    ->menu(['text' => __('Settings'), 'link' => ['user/settings']]);

echo $menu->display();
?>
