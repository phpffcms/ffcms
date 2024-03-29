<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Main\FormSettings $model */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Settings')
])
?>

<?php $this->push('css') ?>
<link rel="stylesheet" href="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/@selectize/selectize/dist/css/selectize.bootstrap5.css" />
<?php $this->stop() ?>

<?php $this->start('body') ?>
<h1><?= __('Settings') ?></h1>

<?= $this->insert('block/breadcrumb', ['breadcrumbs' => [
    __('Main') => ['/'],
    __('Settings')
]]) ?>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs'])
    ->menu(['text' => __('Base'), 'tab' => function() use ($form) {
        return $form->fieldset()->text('baseDomain', ['class' => 'form-control'], __('Main domain of website. Use only in console or cron tasks, if domain cannot be defined from request string')) .
            $form->fieldset()->radio('baseProto', ['options' => ['http', 'https']], __('Main website transfer protocol. Use only if request data is not available in console or cron tasks')) .
            $form->fieldset()->text('basePath', ['class' => 'form-control'], __('FFCMS installation sub-directory, used if installed not in root. Example: /subdir/')) .
            $form->fieldset()->select('timezone', ['options' => DateTimeZone::listIdentifiers(), 'class' => 'selectize-option']) . 
            //$form->fieldset()->select('timezone', ['class' => 'selectize-select', 'options' => DateTimeZone::listIdentifiers()], __('Define website default timezone id')) .
            $form->fieldset()->boolean('userCron', null, __('Initialize cron manager when user load website? Enable this option if you are not configured cron tasks in your operation system')) .
            $form->fieldset()->boolean('debug.all', null, __('Enable debug bar panel for all visitors? Recommended only on development environment')) .
            $form->fieldset()->boolean('testSuite', null, __('Enable codeception test suite adapter? Use this option ONLY to run codeception tests! Disable this option on production'));
    }, 'tabActive' => true])
    ->menu(['text' => __('Themes'), 'tab' => function() use ($form, $model) {
        return $form->fieldset()->select('theme.Front', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Front')], __('Set theme for user part of website')) .
            $form->fieldset()->select('theme.Admin', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Admin')], __('Set theme for admin panel'));
    }])
    ->menu(['text' => __('Mail'), 'tab' => function() use ($form) {
        return '<p>' . __('Configure sendmail over smtp server. You should set host:port and auth data for your smtp server') . '</p>' .
            $form->fieldset()->boolean('mail.enable', ['options' => [0 => 'Disabled', 1 => 'Enabled'], 'optionsKey' => true], __('Is mailing features enabled?')) .
            $form->fieldset()->text('mail.from', ['class' => 'form-control'], __('Set email from which one users will receive messages. Example: zenn1989@gmail.com')) . 
            $form->fieldset()->text('mail.dsn', ['class' => 'form-control'], __('Configure DSN connection string to your email provider. See more at Symfony mailer. Example: smtp://user:pass@smtp.example.com:port'));
    }])
    ->menu(['text' => __('Localization'), 'tab' => function() use ($form) {
        return $form->fieldset()->select('singleLanguage', ['class' => 'form-control', 'options' => \App::$Translate->getAvailableLangs()], __('Default language of website')) .
            $form->fieldset()->boolean('multiLanguage', null, __('Must we use multi language system in site pathway')) .
            $form->fieldset()->text('baseLanguage', ['class' => 'form-control', 'disabled' => null], __('Website base script language. Do not change it')) .
            $form->fieldset()->checkboxes('languages', ['options' => App::$Translate->getAvailableLangs()], __('Website available languages'));

    }])
    ->menu(['text' => __('Database'), 'tab' => function() use ($form) {
        return '<p>' . __('Do not change any information in this tab if you not sure what you do!') . '</p>' .
            $form->fieldset()->select('database.driver', ['class' => 'form-control', 'options' => ['mysql', 'sqlite', 'pgsql']], __('Database connection driver')) .
            $form->fieldset()->text('database.host', ['class' => 'form-control'], __('Database connection host name')) .
            $form->fieldset()->text('database.database', ['class' => 'form-control'], __('Database name or path to sqlite created file database')) .
            $form->fieldset()->text('database.username', ['class' => 'form-control'], __('User name for database connection')) .
            $form->fieldset()->text('database.password', ['class' => 'form-control'], __('Password for user of database connection')) .
            $form->fieldset()->text('database.charset', ['class' => 'form-control']) .
            $form->fieldset()->text('database.collation', ['class' => 'form-control']) .
            $form->fieldset()->text('database.prefix', ['class' => 'form-control'], __('Database tables prefix'));
    }])
    ->menu(['text' => __('Debug'), 'tab' => function() use ($form){
        return '<p>' . __('The key-value of cookie to enable debugging on website') . '. ' . __('If user got this cookie he can see debug bar') . '. ' .
            Url::a(['main/debugcookie'], __('Set cookie for me')) . '</p>' .
            $form->fieldset()->text('debug.cookie.key', ['class' => 'form-control'], __('Set cookie name(key) for enable debug bar panel')) .
            $form->fieldset()->text('debug.cookie.value', ['class' => 'form-control'], __('Set cookie value for enable debug bar panel'));
    }])
    ->menu(['text' => __('Other'), 'tab' => function() use ($form){
        return '<h2>' . __('Captcha') . '</h2>' .
            $form->fieldset()->boolean('captcha.smart', null, __('Use smart captcha features? Captcha will show only after n-count form sending over defined time')) .
            $form->fieldset()->text('captcha.time', null, __('Activity time to count threshold in minutes')) .
            $form->fieldset()->text('captcha.threshold', null, __('Number of threshold attempts to display captcha')) .
            '<h2>' . __('Proxies') . '</h2>' .
            $form->fieldset()->text('trustedProxy', ['class' => 'form-control'], __('Set trusted proxy list to accept X-FORWARDED data. Example: 103.21.244.15,103.22.200.0/22'));
    }])
    ->display(); ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>
<script src="<?= \App::$Alias->scriptUrl ?>/vendor/phpffcms/ffcms-assets/node_modules/@selectize/selectize/dist/js/standalone/selectize.min.js"></script>
<script>
$(document).ready(function(){
    $('.selectize-option').selectize({
        sortField: 'text'
    });
});
</script>

<?php $this->stop() ?>