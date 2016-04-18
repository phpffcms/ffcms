<?php
use Ffcms\Core\Helper\Url;

/** @var object $this */

$this->title = __('Route saved');
?>
<h1><?= __('Congratulations!') ?></h1>
<hr />
<p><?= __('Route are successful saved! Wait 5 second to update configurations') ?></p>
<?= Url::link(['main/routing'], __('Reload'), ['class' => 'btn btn-primary']); ?>
<script>
setTimeout(function () {
	window.location.replace("<?= Url::to('main/routing') ?>");
 }, 5000);
</script>