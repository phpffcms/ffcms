<?php

/** @var $model \Apps\Model\Admin\Main\FormSettings */
/** @var $this \Ffcms\Core\Arch\View */
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

$form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']);
echo $form->start();
$baseTab = $form->field('basePath', 'text', ['class' => 'form-control'], __('FFCMS installation sub-directory, used if installed not in root. Example: /subdir/'));
$baseTab .= $form->field('adminEmail', 'email', ['class' => 'form-control'], __('Define administrator email. Used in mailing functions. Other mail settings in /Private/Config/Object.php'));
$baseTab .= $form->field('timezone', 'select', ['class' => 'form-control', 'options' => DateTimeZone::listIdentifiers()], __('Define website default timezone id'));
$baseTab .= $form->field('debug.all', 'checkbox', null, __('Enable debug bar panel for all visitors? Recommended only on development environment'));

$themeTab = $form->field('theme.Front', 'select', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Front')]);
$themeTab .= $form->field('theme.Admin', 'select', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Admin')]);

$debugTab = '<p>' . __('The key-value of cookie to enable debugging on website') . '. ' . __('If user got this cookie he can see debug bar') . '. ' .
    Url::link(['main/debugcookie'], __('Set cookie for me')) . '</p>';
$debugTab .= $form->field('debug.cookie.key', 'text', ['class' => 'form-control'], __('Set cookie name(key) for enable debug bar panel'));
$debugTab .= $form->field('debug.cookie.value', 'text', ['class' => 'form-control'], __('Set cookie value for enable debug bar panel'));

$langTab = $form->field('singleLanguage', 'select', ['class' => 'form-control', 'options' => \App::$Translate->getAvailableLangs()], __('Default language of website'));
$langTab .= $form->field('multiLanguage', 'checkbox', null, __('Must we use multi language system in site pathway'));
$langTab .= $form->field('baseLanguage', 'text', ['class' => 'form-control', 'disabled' => null], __('Website base script language. Do not change it'));
$langTab .= $form->field('languages', 'checkboxes', ['options' => ['ru', 'en']], __('Website available languages'));

$databaseTab = '<p>' . __('Do not change any information in this tab if you not sure what you do!') . '</p>';
$databaseTab .= $form->field('database.driver', 'select', ['class' => 'form-control', 'options' => ['mysql', 'sqlite', 'pgsql']], __('Database connection driver'));
$databaseTab .= $form->field('database.host', 'text', ['class' => 'form-control'], __('Database connection host name'));
$databaseTab .= $form->field('database.database', 'text', ['class' => 'form-control'], __('Database name or path to sqlite created file database'));
$databaseTab .= $form->field('database.username', 'text', ['class' => 'form-control'], __('User name for database connection'));
$databaseTab .= $form->field('database.password', 'text', ['class' => 'form-control'], __('Password for user of database connection'));
$databaseTab .= $form->field('database.charset', 'text', ['class' => 'form-control']);
$databaseTab .= $form->field('database.collation', 'text', ['class' => 'form-control']);
$databaseTab .= $form->field('database.prefix', 'text', ['class' => 'form-control'], __('Database tables prefix'));

$otherTab = '<p>' . __('There you can change specified configs depends of other platforms. GA = google analytics.') . '</p>';
$otherTab .= $form->field('gaClientId', 'text', ['class' => 'form-control'], __('Google oAuth2 client id. This id will be used to display google.analytics info. Client ID looks like: xxxxxx.apps.googleusercontent.com'));
$otherTab .= $form->field('gaTrackId', 'text', ['class' => 'form-control'], __('Set google analytics tracking id for your website. Track id looks like: UA-XXXXXX-Y'));
$otherTab .= $form->field('trustedProxy', 'text', ['class' => 'form-control'], __('Set trusted proxy list to accept X-FORWARDED data. Example: 103.21.244.15,103.22.200.0/22'));

?>

<?php echo \Ffcms\Core\Helper\HTML\Bootstrap\Nav::display([
    'property' => ['class' => 'nav-tabs'],
    'tabAnchor' => 'obj-settings',
    'items' => [
        ['type' => 'tab', 'text' => __('Base'), 'content' => $baseTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Themes'), 'content' => $themeTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Localization'), 'content' => $langTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Database'), 'content' => $databaseTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Debug'), 'content' => $debugTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Other'), 'content' => $otherTab, 'html' => true, '!secure' => true]
    ]
]); ?>

    <div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>