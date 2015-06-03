<?php
use Ffcms\Core\Helper\Url;
?>
<h1>Congratulations</h1>
<hr />
<p>Settings are successful saved! Wait 5 second to update configurations.</p>
<?= Url::link(['main/settings'], 'Reload', ['class' => 'btn btn-primary']); ?>