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

$baseTab = $form->field->text('baseDomain', ['class' => 'form-control'], __('Main domain of website. Use only in console or cron tasks, if domain cannot be defined from request string'));
$baseTab .= $form->field->radio('baseProto', ['options' => ['http', 'https']], __('Main website transfer protocol. Use only if request data is not available in console or cron tasks'));
$baseTab .= $form->field->text('basePath', ['class' => 'form-control'], __('FFCMS installation sub-directory, used if installed not in root. Example: /subdir/'));
$baseTab .= $form->field->select('timezone', ['class' => 'form-control', 'options' => DateTimeZone::listIdentifiers()], __('Define website default timezone id'));
$baseTab .= $form->field->checkbox('userCron', null, __('Initialize cron manager when user load website? Enable this option if you are not configured cron tasks in your operation system'));
$baseTab .= $form->field->checkbox('debug.all', null, __('Enable debug bar panel for all visitors? Recommended only on development environment'));
$baseTab .= $form->field->checkbox('testSuite', null, __('Enable codeception test suite adapter? Use this option ONLY to run codeception tests! Disable this option on production'));

$themeTab = $form->field->select('theme.Front', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Front')], __('Set theme for user part of website'));
$themeTab .= $form->field->select('theme.Admin', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Admin')], __('Set theme for admin panel'));

$debugTab = '<p>' . __('The key-value of cookie to enable debugging on website') . '. ' . __('If user got this cookie he can see debug bar') . '. ' .
    Url::link(['main/debugcookie'], __('Set cookie for me')) . '</p>';
$debugTab .= $form->field->text('debug.cookie.key', ['class' => 'form-control'], __('Set cookie name(key) for enable debug bar panel'));
$debugTab .= $form->field->text('debug.cookie.value', ['class' => 'form-control'], __('Set cookie value for enable debug bar panel'));

$langTab = $form->field->select('singleLanguage', ['class' => 'form-control', 'options' => \App::$Translate->getAvailableLangs()], __('Default language of website'));
$langTab .= $form->field->checkbox('multiLanguage', null, __('Must we use multi language system in site pathway'));
$langTab .= $form->field('baseLanguage', 'text', ['class' => 'form-control', 'disabled' => null], __('Website base script language. Do not change it'));
$langTab .= $form->field('languages', 'checkboxes', ['options' => App::$Translate->getAvailableLangs()], __('Website available languages'));

$databaseTab = '<p>' . __('Do not change any information in this tab if you not sure what you do!') . '</p>';
$databaseTab .= $form->field('database.driver', 'select', ['class' => 'form-control', 'options' => ['mysql', 'sqlite', 'pgsql']], __('Database connection driver'));
$databaseTab .= $form->field('database.host', 'text', ['class' => 'form-control'], __('Database connection host name'));
$databaseTab .= $form->field('database.database', 'text', ['class' => 'form-control'], __('Database name or path to sqlite created file database'));
$databaseTab .= $form->field('database.username', 'text', ['class' => 'form-control'], __('User name for database connection'));
$databaseTab .= $form->field('database.password', 'text', ['class' => 'form-control'], __('Password for user of database connection'));
$databaseTab .= $form->field('database.charset', 'text', ['class' => 'form-control']);
$databaseTab .= $form->field('database.collation', 'text', ['class' => 'form-control']);
$databaseTab .= $form->field('database.prefix', 'text', ['class' => 'form-control'], __('Database tables prefix'));

$mailTab = '<p>' . __('Configure sendmail over smtp server. You should set host:port and auth data for your smtp server') . '</p>';
$mailTab .= $form->field->text('mail.host', ['class' => 'form-control'], __('Set SMTP hostname or ip'));
$mailTab .= $form->field->text('mail.port', ['class' => 'form-control'], __('Set SMTP connection port'));
$mailTab .= $form->field->select('mail.encrypt', ['class' => 'form-control', 'options' => ['tls', 'ssl', 'none']], __('Set encryption method for your smtp server. For remote service we are strongly recommend use tls/ssl encryption'));
$mailTab .= $form->field->text('mail.user', ['class' => 'form-control'], __('Set auth user name if required'));
$mailTab .= $form->field->text('mail.password', ['class' => 'form-control'], __('Set auth user password if exist'));

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
        ['type' => 'tab', 'text' => __('Mail'), 'content' => $mailTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Localization'), 'content' => $langTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Database'), 'content' => $databaseTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Debug'), 'content' => $debugTab, 'html' => true, '!secure' => true],
        ['type' => 'tab', 'text' => __('Other'), 'content' => $otherTab, 'html' => true, '!secure' => true]
    ]
]); ?>

    <div class="col-md-9 col-md-offset-3"><?= $form->submitButton(__('Save'), ['class' => 'btn btn-primary']) ?></div>

<?= $form->finish() ?>