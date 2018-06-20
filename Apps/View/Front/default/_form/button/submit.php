<?php
/** @var \Ffcms\Templex\Template\Template $this */

/** @var string|null $text */
/** @var array|null $properties */

$properties['type'] = 'submit';
$properties['value'] = $text;
?>

<div class="row">
    <div class="col-md-9 offset-md-3">
        <?= (new \Ffcms\Templex\Helper\Html\Dom())->input($properties) ?>
    </div>
</div>
