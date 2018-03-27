<?php
/** @var \Ffcms\Templex\Template\Template $this */

/** @var string|null $text */
/** @var array|null $properties */

?>

<div class="row">
    <div class="col-md-9 offset-md-3">
        <?= (new \Ffcms\Templex\Helper\Html\Dom())->button(function() use ($text){
            return $text;
        }, $properties); ?>
    </div>
</div>
