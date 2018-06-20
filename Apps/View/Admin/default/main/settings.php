<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Main\FormSettings $model */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Settings')
])
?>

<?php $this->start('body') ?>
<h1><?= __('Settings') ?></h1>
<hr />

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $this->bootstrap()->nav('ul', ['class' => 'nav-tabs'])
    ->menu(['text' => __('Base'), 'tab' => function() use ($form) {
        return $form->fieldset()->text('baseDomain', ['class' => 'form-control'], __('Main domain of website. Use only in console or cron tasks, if domain cannot be defined from request string')) .
            $form->fieldset()->radio('baseProto', ['options' => ['http', 'https']], __('Main website transfer protocol. Use only if request data is not available in console or cron tasks')) .
            $form->fieldset()->text('basePath', ['class' => 'form-control'], __('FFCMS installation sub-directory, used if installed not in root. Example: /subdir/')) .
            $form->fieldset()->select('timezone', ['class' => 'form-control', 'options' => DateTimeZone::listIdentifiers()], __('Define website default timezone id')) .
            $form->fieldset()->boolean('userCron', null, __('Initialize cron manager when user load website? Enable this option if you are not configured cron tasks in your operation system')) .
            $form->fieldset()->boolean('debug.all', null, __('Enable debug bar panel for all visitors? Recommended only on development environment')) .
            $form->fieldset()->boolean('testSuite', null, __('Enable codeception test suite adapter? Use this option ONLY to run codeception tests! Disable this option on production'));
    }, 'linkProperties' => ['class' => 'nav-link active']])
    ->menu(['text' => __('Themes'), 'tab' => function() use ($form, $model) {
        return $form->fieldset()->select('theme.Front', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Front')], __('Set theme for user part of website')) .
            $form->fieldset()->select('theme.Admin', ['class' => 'form-control', 'options' => $model->getAvailableThemes('Admin')], __('Set theme for admin panel'));
    }])
    ->menu(['text' => __('Mail'), 'tab' => function() use ($form) {
        return '<p>' . __('Configure sendmail over smtp server. You should set host:port and auth data for your smtp server') . '</p>' .
            $form->fieldset()->text('mail.host', ['class' => 'form-control'], __('Set SMTP hostname or ip')) .
            $form->fieldset()->text('mail.port', ['class' => 'form-control'], __('Set SMTP connection port')) .
            $form->fieldset()->select('mail.encrypt', ['class' => 'form-control', 'options' => ['tls', 'ssl', 'none']], __('Set encryption method for your smtp server. For remote service we are strongly recommend use tls/ssl encryption')) .
            $form->fieldset()->text('mail.user', ['class' => 'form-control'], __('Set auth user name if required')) .
            $form->fieldset()->text('mail.password', ['class' => 'form-control'], __('Set auth user password if exist'));
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
        return '<p>' . __('There you can change specified configs depends of other platforms. GA = google analytics.') . '</p>' .
            $form->fieldset()->text('gaClientId', ['class' => 'form-control'], __('Google oAuth2 client id. This id will be used to display google.analytics info. Client ID looks like: xxxxxx.apps.googleusercontent.com')) .
            $form->fieldset()->text('gaTrackId', ['class' => 'form-control'], __('Set google analytics tracking id for your website. Track id looks like: UA-XXXXXX-Y')) .
            $form->fieldset()->text('trustedProxy', ['class' => 'form-control'], __('Set trusted proxy list to accept X-FORWARDED data. Example: 103.21.244.15,103.22.200.0/22'));
    }])
    ->display(); ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>
