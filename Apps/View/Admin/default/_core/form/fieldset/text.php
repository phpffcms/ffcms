<?php
/** @var \Ffcms\Templex\Template\Template $this */

// input type=text

/** @var string $label */
/** @var array $properties */
/** @var string|null $helper */
/** @var array|null $labelProperties */
/** @var \Ffcms\Templex\Helper\Html\Form\Field\Text $field */

if (!isset($labelProperties['class'])) {
    $labelProperties['class'] = 'col-md-3 control-label';
}
$labelProperties['for'] = $field->getUniqueFieldId();
if (!isset($properties['class'])) {
    $properties['class'] = 'form-control';
}
?>
<div class="form-group row">
    <?= (new \Ffcms\Templex\Helper\Html\Dom())->label(function() use ($label) {
        return $label;
    }, $labelProperties) ?>
    <div class="col-md-9">
        <?= $field->html($properties) ?>
        <?php if ($helper): ?>
        <small class="form-text text-muted"><?= $helper ?></small>
        <?php endif; ?>
    </div>
</div>