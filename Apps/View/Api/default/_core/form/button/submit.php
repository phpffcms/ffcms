<?php
/** @var \Ffcms\Templex\Template\Template $this */

/** @var string|null $text */
/** @var array|null $properties */

$properties['type'] = 'submit';
$properties['value'] = $text;
?>

<?= (new \Ffcms\Templex\Helper\Html\Dom())->input($properties) ?>