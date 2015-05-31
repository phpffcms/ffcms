<?php

/** @var $model object */
/** @var $this object */
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Settings')
];
$this->title = __('Settings');

?>

<h1><?= __('Settings') ?></h1>
<hr />

<?php

$form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post', 'action' => '', 'enctype' => 'multipart/form-data']);
$baseTab = $form->field('basePath', 'inputText', ['class' => 'form-control'], __('FFCMS installation path to DOCUMENT_ROOT. Used if installed not in root. Example: /subdir/'));
$baseTab .= $form->field('siteIndex', 'inputText', ['class' => 'form-control'], __('Define controller::action to display on main page in position $body'));
$baseTab .= $form->field('passwordSalt', 'inputText', ['class' => 'form-control', 'disabled' => null], __('Password crypt security salt. Do not change this value'));
$baseTab .= $form->field('debug_all', 'checkbox', null, __('Enable debug bar panel for all visitors? Recommended only on development environment'));

$themeTab = $form->field('theme_Front', 'select', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Front')]);
$themeTab .= $form->field('theme_Admin', 'select', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Admin')]);

$debugTab = $form->field('debug_cookie_key', 'inputText', ['class' => 'form-control'], __('Set cookie name(key) for enable debug bar panel'));
$debugTab .= $form->field('debug_cookie_value', 'inputText', ['class' => 'form-control'], __('Set cookie value for enable debug bar panel'));

$debugTab .= '<div class="col-md-12"><div class="pull-right">
' . Url::link(['main/debugcookie'], 'Set cookie for me') . '
</div></div>';
?>

<?php echo \Ffcms\Core\Helper\HTML\Bootstrap\Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'tabAnchor' => 'obj-settings',
    'items' => [
        ['type' => 'tab', 'text' => 'Base', 'content' => $baseTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => 'Themes', 'content' => $themeTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => 'Languages', 'content' => 'null'],
        ['type' => 'tab', 'text' => 'Database', 'content' => 'null'],
        ['type' => 'tab', 'text' => 'Debug', 'content' => $debugTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => 'Other', 'content' => 'This is an other content of other tab!']
    ]
]); ?>

    <div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>