<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var string $selector */
/** @var string $text */
/** @var string $query */
/** @var array $url */
/** @var array $properties */

$rndId = 'btn_selectize_submit_' . mt_rand(999, 999999);
$properties['id'] = $rndId;

echo (new \Ffcms\Templex\Helper\Html\Dom())->button(function() use ($text) {
    return $text;
}, $properties);

$address = \Ffcms\Templex\Url\Url::link($url);
?>


<script>
$(document).ready(function(){
    $('#<?= $rndId ?>').on('click', function() {
        var targetUrl = '<?= $address ?>?jselect=true';
        $('<?= $selector ?>').each(function(item) {
            if ($(this).is(':checked')) {
                targetUrl += '&<?=$query?>[]=' + $(this).attr('value');
            }
            window.location = targetUrl;
        });
    })
});
</script>
