<?php

/** @var bool $validator */
/** @var \Ffcms\Templex\Helper\Html\Form\ModelInterface $model */
?>

<?php if ($validator && $model->getBadAttributes() && is_array($model->getBadAttributes())): ?>
    <script>
    <?php foreach ($model->getBadAttributes() as $attr): ?>
        var invalidField = document.getElementById("<?= $model->getFormName() ?>-<?= $attr ?>");
        if (invalidField)
            invalidField.classList.add('is-invalid');
    <?php endforeach; ?>
    // remove "is-invalid" on change
    $(function(){
        $('#<?= $model->getFormName()?> input').change(function(){
            $(this).removeClass('is-invalid');
        });
    });
    </script>
<?php endif; ?>
</form>
