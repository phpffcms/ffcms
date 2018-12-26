<?php

use Ffcms\Core\App;

// captcha features for ffcms

/** @var \Ffcms\Templex\Helper\Html\Form $form */

if (!isset($name)) {
    $name = 'captcha';
}

$html = App::$Captcha->get();
?>

<?php if (App::$Captcha->isFull()): ?>
    <div class="col-md-9 offset-md-3"><?= $html ?></div>
<?php else: ?>
    <div class="row">
        <div class="col-md-9 offset-md-3">
            <img src="<?= $html ?>" alt="captcha" onClick="this.src='<?=$html?>&rnd='+Math.random()" />
        </div>
    </div>
    <?= $form->fieldset()->text($name, null, __('Enter data from security image to prove that you are human. If you can\'t read symbols - click on image to reload')) ?>
<?php endif; ?>