<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Install\Main\FormInstall $model */

$this->layout('_layouts/default', [
    'title' => 'Install'
]);

$form = $this->form($model);
?>

<?php $this->start('body') ?>
<h1><?= __('FFCMS installation') ?></h1>
<hr />
<?= $form->start() ?>

<h2><?= __('Database configurations') ?></h2>
<?= $form->fieldset()->select('db.driver', ['options' => ['mysql', 'pgsql']], __('Select database server driver (type). Recommended - mysql')) ?>
<?= $form->fieldset()->text('db.host', ['placeHolder' => 'localhost'], __('Hostname or ip:port address of your database server')) ?>
<?= $form->fieldset()->text('db.username', ['placeHolder' => 'root'], __('User name for database connection')) ?>
<?= $form->fieldset()->text('db.password', ['placeHolder' => 'rootpwd'], __('User password for database connection')) ?>
<?= $form->fieldset()->text('db.database', ['placeHolder' => 'ffdb'], __('Database name in database server')) ?>
<?= $form->fieldset()->text('db.prefix', ['placeHolder' => 'ffcms_'], __('Table prefix for multiple usage of single database (example: ffcms_)')) ?>

<h2><?= __('General configurations') ?></h2>
<?= $form->fieldset()->radio('mainpage', ['options' => ['none' => __('Developer'), 'news' => __('News list'), 'about' => __('About page')], 'optionsKey' => true], __('Select what we should display on the main page. You can always change it in routing configurations')) ?>
<?= $form->fieldset()->select('singleLanguage', ['class' => 'form-control', 'options' => \App::$Translate->getAvailableLangs()], __('Default language of website')); ?>
<?= $form->fieldset()->boolean('multiLanguage', ['checked' => true], __('Must we use multi language system in site pathway')); ?>

<h2><?= __('SMTP mail configurations') ?></h2>
<?= $form->fieldset()->boolean('mail.enable', ['options' => [0 => 'Disabled', 1 => 'Enabled'], 'optionsKey' => true], __('Is mailing features enabled?')) ?>
<?= $form->fieldset()->text('mail.from', ['class' => 'form-control'], __('Set "sended from" email address. Example: zenn1989@gmail.com')) ?>
<?= $form->fieldset()->text('mail.dsn', ['class' => 'form-control'], __('Set DSN format connection string. See symfony/mailer for more. Example: smtp://user:pass@smtp.example.com:port')) ?>


<h2><?= __('Administrator account') ?></h2>
<?= $form->fieldset()->text('user.email', ['class' => 'form-control'], __('Set administrator account email')) ?>
<?= $form->fieldset()->password('user.password', ['class' => 'form-control'], __('Set administrator password')) ?>
<?= $form->fieldset()->password('user.repassword', ['class' => 'form-control'], __('Repeat administrator password')) ?>
<?= $form->button()->submit(__('Install'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>

<script>
    $(document).ready(function(){
        var status = $('#formInstall-mail-enable').is(':checked');
        if (status !== true) {
            $('#formInstall-mail-from,#formInstall-mail-dsn').attr('disabled', 'disabled');
        }

        $('#formInstall-mail-enable').change(function(){
            var chk = this.checked;
            if (!chk) {
                $('#formInstall-mail-from,#formInstall-mail-dsn').attr('disabled', 'disabled');
            } else {
                $('#formInstall-mail-from,#formInstall-mail-dsn').removeAttr('disabled');
            }
        })

    });
</script>

<?php $this->stop() ?>
