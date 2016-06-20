<?php
/** @var $model \Apps\Model\Install\Main\FormInstall */
$form = new \Ffcms\Core\Helper\HTML\Form($model, ['class' => 'form-horizontal', 'method' => 'post']);
?>

<h1><?= __('FFCMS installation') ?></h1>
<hr />

<?= $form->start() ?>

<h2><?= __('Database configurations') ?></h2>
<?= $form->field('db.driver', 'select', ['class' => 'form-control', 'options' => ['mysql', 'pgsql']], __('Select database server driver (type). Recommended - mysql')) ?>
<?= $form->field('db.host', 'text', ['class' => 'form-control', 'placeHolder' => 'localhost'], __('Hostname or ip:port address of your database server')) ?>
<?= $form->field('db.username', 'text', ['class' => 'form-control', 'placeHolder' => 'root'], __('User name for database connection')) ?>
<?= $form->field('db.password', 'text', ['class' => 'form-control', 'placeHolder' => 'rootpwd'], __('User password for database connection')) ?>
<?= $form->field('db.database', 'text', ['class' => 'form-control', 'placeHolder' => 'ffdb'], __('Database name in database server')) ?>
<?= $form->field('db.prefix', 'text', ['class' => 'form-control', 'placeHolder' => 'ffcms_'], __('Table prefix for multiple usage of single database (example: ffcms_)')) ?>

<h2><?= __('General configurations') ?></h2>
<?= $form->field('mainpage', 'radio', ['options' => ['none' => __('Empty'), 'news' => __('News list'), 'about' => __('About page')], 'optionsKey' => true], __('Select what we should display on the main page. You can always change it in routing configurations')) ?>
<?= $form->field('email', 'text', ['class' => 'form-control', 'placeHolder' => 'example@site.com'], __('Set your general email to use in sendFrom for mailing functions')) ?>
<?= $form->field('singleLanguage', 'select', ['class' => 'form-control', 'options' => \App::$Translate->getAvailableLangs()], __('Default language of website')); ?>
<?= $form->field('multiLanguage', 'checkbox', ['checked' => true], __('Must we use multi language system in site pathway')); ?>

<h2><?= __('Administrator account') ?></h2>

<?= $form->field('user.login', 'text', ['class' => 'form-control'], __('Set administrator account login')) ?>
<?= $form->field('user.email', 'text', ['class' => 'form-control'], __('Set administrator account email')) ?>
<?= $form->field('user.password', 'password', ['class' => 'form-control'], __('Set administrator password')) ?>
<?= $form->field('user.repassword', 'password', ['class' => 'form-control'], __('Repeat administrator password')) ?>
<?= $form->submitButton(__('Install'), ['class' => 'btn btn-primary']) ?>

<?= $form->finish() ?>
