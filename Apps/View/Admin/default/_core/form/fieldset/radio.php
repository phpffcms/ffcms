<?php

use Ffcms\Templex\Helper\Html\Dom;

/** @var \Ffcms\Templex\Template\Template $this */

// input type=radio

/** @var string $label */
/** @var array $properties */
/** @var string|null $helper */
/** @var array|null $labelProperties */
/** @var \Ffcms\Templex\Helper\Html\Form\Field\Radio $field */

if (!isset($labelProperties['class'])) {
    $labelProperties['class'] = 'col-md-3 control-label col-form-label';
}

if (!isset($properties['class'])) {
    $properties['class'] = 'form-check-input';
}
$properties['arrayLabelProperties']['class'] = 'form-check-label';

$inputs = $field->asArray($properties);
if (!$inputs || !is_array($inputs)) {
    return null;
}
?>
<div class="form-group row">
    <?= (new \Ffcms\Templex\Helper\Html\Dom())->label(function() use ($label) {
        return $label;
    }, $labelProperties) ?>
    <div class="col-md-9">
        <?php
        foreach ($inputs as $input) {
            if (!is_string($input) || strlen($input) < 1) {
                continue;
            }
            echo (new Dom())->div(function() use ($input) {
                return $input;
            }, ['class' => 'form-check form-check-inline']);
        }
        ?>
        <?php if ($helper): ?>
            <p class="form-text"><?= $helper ?></p>
        <?php endif; ?>
    </div>
</div>