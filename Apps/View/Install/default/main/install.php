<?php
/** @var $model \Apps\Model\Install\Main\FormInstall */
$form = new \Ffcms\Core\Helper\HTML\Form($model, ['class' => 'form-horizontal', 'method' => 'post']);
?>

<h1><?= __('FFCMS installation') ?></h1>
<hr />

<?= $form->start() ?>

<h2><?= __('Database configurations') ?></h2>
<?= $form->field->select('db.driver', ['class' => 'form-control', 'options' => ['mysql', 'pgsql']], __('Select database server driver (type). Recommended - mysql')) ?>
<?= $form->field->text('db.host', ['class' => 'form-control', 'placeHolder' => 'localhost'], __('Hostname or ip:port address of your database server')) ?>
<?= $form->field->text('db.username', ['class' => 'form-control', 'placeHolder' => 'root'], __('User name for database connection')) ?>
<?= $form->field->text('db.password', ['class' => 'form-control', 'placeHolder' => 'rootpwd'], __('User password for database connection')) ?>
<?= $form->field->text('db.database', ['class' => 'form-control', 'placeHolder' => 'ffdb'], __('Database name in database server')) ?>
<?= $form->field->text('db.prefix', ['class' => 'form-control', 'placeHolder' => 'ffcms_'], __('Table prefix for multiple usage of single database (example: ffcms_)')) ?>

<h2><?= __('General configurations') ?></h2>
<?= $form->field->radio('mainpage', ['options' => ['none' => __('Developer'), 'news' => __('News list'), 'about' => __('About page')], 'optionsKey' => true], __('Select what we should display on the main page. You can always change it in routing configurations')) ?>
<?= $form->field->text('email', ['class' => 'form-control', 'placeHolder' => 'example@site.com'], __('Set your general email to use in sendFrom for mailing functions')) ?>
<?= $form->field->select('singleLanguage', ['class' => 'form-control', 'options' => \App::$Translate->getAvailableLangs()], __('Default language of website')); ?>
<?= $form->field->checkbox('multiLanguage', ['checked' => true], __('Must we use multi language system in site pathway')); ?>

<h2><?= __('SMTP mail configurations') ?></h2>
<?= $form->field->text('mail.host', ['class' => 'form-control'], __('Set SMTP hostname or ip')) ?>
<?= $form->field->text('mail.port', ['class' => 'form-control'], __('Set SMTP connection port')) ?>
<?= $form->field->select('mail.encrypt', ['class' => 'form-control', 'options' => ['tls', 'ssl', 'none']], __('Set encryption method for your smtp server. For remote service we are strongly recommend use tls/ssl encryption')); ?>
<?= $form->field->text('mail.user', ['class' => 'form-control'], __('Set auth user name if required')); ?>
<?= $form->field->text('mail.password', ['class' => 'form-control'], __('Set auth user password if exist')); ?>

<h2><?= __('Administrator account') ?></h2>

<?= $form->field->text('user.login', ['class' => 'form-control'], __('Set administrator account login')) ?>
<?= $form->field->text('user.email', ['class' => 'form-control'], __('Set administrator account email')) ?>
<?= $form->field->password('user.password', ['class' => 'form-control'], __('Set administrator password')) ?>
<?= $form->field->password('user.repassword', ['class' => 'form-control'], __('Repeat administrator password')) ?>
<?= $form->submitButton(__('Install'), ['class' => 'btn btn-primary']) ?>

<?= $form->finish() ?>
