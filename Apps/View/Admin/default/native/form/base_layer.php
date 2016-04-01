<?php 
/** @var string $name */
/** @var string $label */
/** @var string $item */
/** @var string $help */
?>

<div class="form-group">
	<label for="<?= $name ?>" class="col-md-3 control-label"><?= $label ?></label>
	<div class="col-md-9">
		<?= $item ?>
		<p class="help-block"><?= $help ?></p>
	</div>
</div>