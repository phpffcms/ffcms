<?php

/** @var array|null $properties */
/** @var string|null $csrfField */

use Ffcms\Templex\Helper\Html\Dom;

?>

<form <?= Dom::applyProperties($properties)?>>
<?= $csrfField ?>
