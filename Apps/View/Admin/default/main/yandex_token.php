<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\Model\Admin\Main\FormYandexToken $model */

use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Yandex.Metrika - token'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Main'),
        __('Yandex metrika - step 2')
    ]
]);
?>

<?php $this->start('body') ?>
<h1><?= __('Yandex token') ?></h1>
<p><?= __('This is callback form - you are successfully got a new yandex token! Save it!') ?></p>

<?php $form = $this->form($model) ?>

<?= $form->start() ?>

<?= $form->fieldset()->text('token', ['readonly' => 'readonly']) ?>
<?= $form->fieldset()->text('expires', ['readonly' => 'readonly']) ?>

<?= $form->button()->submit(__('Save'), ['class' => 'btn btn-primary']) ?>

<?= $form->stop() ?>

<?php $this->stop() ?>

<?php $this->push('javascript') ?>
<script type="text/javascript">
    $(document).ready(function(){
        var reqHash = window.location.hash.substring(1);
        var params = {};
        reqHash.split('&').map(hk => {
            let temp = hk.split('=');
            params[temp[0]] = temp[1]
        });

        if (!params.access_token || params.access_token.length < 20) {
            alert('No access token exist! Error!');
            return false;
        } else {
            $('#yaTokenForm-token').val(params.access_token);
        }

        $('#yaTokenForm-expires').val(params.expires_in);
    });
</script>
<?php $this->stop() ?>
