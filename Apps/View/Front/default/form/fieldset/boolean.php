<?php
/** @var \Ffcms\Templex\Template\Template $this */

// input type=checkbox implementation of checked/notchecked single checkbox

/** @var string $label */
/** @var array $properties */
/** @var string|null $helper */
/** @var \Ffcms\Templex\Helper\Html\Form\Field\Boolean $field */

$labelProperties['for'] = $field->getUniqueFieldId();
?>
<div class="form-group row">
    <div class="col-md-9 offset-md-3">
        <?= $field->html($properties) ?>
        <?= (new \Ffcms\Templex\Helper\Html\Dom())->label(function() use ($label) {
            return $label;
        }, $labelProperties) ?>
        <?php if ($helper): ?>
            <p class="form-text"><?= $helper ?></p>
        <?php endif; ?>
    </div>
</div>