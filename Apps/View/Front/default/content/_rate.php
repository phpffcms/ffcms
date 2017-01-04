<?php
/** @var int $rating */
/** @var bool $canRate */
/** @var int $id */

$cssRatingClass = 'label-info';
$numericRating = 0;
if ($rating > 0) {
    $cssRatingClass = 'label-success';
    $numericRating = '+' . $rating;
} elseif($rating < 0) {
    $cssRatingClass = 'label-danger';
    $numericRating = $rating;
}
?>
<span class="rating" style="margin-right: 20px;">
	<?php if ($canRate === true):?>
	<span class="change-rate minus hide-rate-<?= $id?> label label-danger" id="content-<?= $id ?>" data-toggle="tooltip" title="<?= __('Dislike this') ?>"><i class="glyphicon glyphicon-arrow-down"></i></span>
	<?php endif; ?>
	<span class="label <?= $cssRatingClass ?>" data-toggle="tooltip" title="<?= __('Current content rating') ?>" id="rate-value-<?= $id ?>"><?= $numericRating ?></span>
	<?php if ($canRate === true):?>
	<span class="change-rate plus hide-rate-<?= $id?> label label-success" id="content-<?= $id ?>" data-toggle="tooltip" title="<?= __('Like this') ?>"><i class="glyphicon glyphicon-arrow-up"></i></span>&nbsp;
	<?php endif; ?>
</span>