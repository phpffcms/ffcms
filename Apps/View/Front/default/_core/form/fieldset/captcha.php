<?php

use Ffcms\Core\App;

// captcha features for ffcms

/** @var \Ffcms\Templex\Helper\Html\Form $form */

if (!isset($name)) {
    $name = 'captcha';
}

$html = App::$Captcha->get();
?>


<!-- hidden modal for captcha features -->
<div class="modal fade" id="captchaModal" tabindex="-1" role="dialog" aria-labelledby="captchaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="captchaModalLabel">Spam protection</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>You need to pass robot protection. Please, fill solution before.</p>
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
            </div>
        </div>
    </div>
</div>


<script>
    var formId = '<?= $form->model()->getFormName() ?>';
    $(function(){
        var captchaFilled = false;
        $('#' + formId).on('submit', function(e){
            if (captchaFilled)
                return true;

            var form = $(this);

            e.preventDefault();
            // check if captcha test required
            $.ajax({
                dataType: 'json',
                url: script_url + '/api/captcha/verify/<?=$form->model()->_csrf_token?>?lang=' + script_lang,
                success: function(data){
                    if (data.required === false) {
                        captchaFilled = true;
                        form.submit();
                    } else {
                        $('#captchaModal').modal('show');
                    }
                }
            });
        });
    });
</script>
