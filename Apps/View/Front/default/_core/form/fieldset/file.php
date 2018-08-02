<?php
/** @var \Ffcms\Templex\Template\Template $this */

// input type=file

/** @var string $label */
/** @var array $properties */
/** @var string|null $helper */
/** @var array|null $labelProperties */
/** @var \Ffcms\Templex\Helper\Html\Form\Field\File $field */

if (!isset($labelProperties['class'])) {
    $labelProperties['class'] = 'col-md-3 control-label';
}
$labelProperties['for'] = $field->getUniqueFieldId();
?>
<div class="form-group row">
    <?= (new \Ffcms\Templex\Helper\Html\Dom())->label(function() use ($label) {
        return $label;
    }, $labelProperties) ?>
    <div class="col-md-9">
        <?= $field->html($properties) ?>
        <?php if ($helper): ?>
            <p class="form-text"><?= $helper ?></p>
        <?php endif; ?>
    </div>
</div>