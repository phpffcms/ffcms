<?php

use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\Url;

/** @var \Apps\Model\Front\Feedback\FormFeedbackAdd $model */
/** @var bool $useCaptcha */

$this->title = __('Feedback');
$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    __('Feedback')
]

?>
<h1><?= __('Feedback') ?></h1>
<?php
if (\App::$User->isAuth()) {
    echo $this->render('feedback/_authTabs');
} else {
    echo "<hr />";
}
?>
<?php $form = new Form($model, ['class' => 'form-horizontal', 'method' => 'post']) ?>
<?= $form->start() ?>

<?= $form->field('name', 'text', ['class' => 'form-control'], __('Enter your name, used in feedback emails')) ?>
<?= $form->field('email', 'email', ['class' => 'form-control'], __('Enter the email to contact with you')) ?>
<?= $form->field('message', 'textarea', ['class' => 'form-control', 'rows' => 7], __('Enter your feedback request text. Please, dont use HTML or other codes.')) ?>

<?php if ($useCaptcha === true) {
    if (\App::$Captcha->isFull()) {
        echo '<div class="col-md-offset-3 col-md-9">' . \App::$Captcha->get() . '</div>';
    } else {
        echo $form->field('captcha', 'captcha', ['class' => 'form-control'], __('Enter data from security image to prove that you are human. If you can\'t read symbols - click on image to reload'));
    }
} ?>

<div class="col-md-offset-3 col-md-9">
    <?= $form->submitButton(__('Send'), ['class' => 'btn btn-primary']) ?>
</div>


<?= $form->finish() ?>
