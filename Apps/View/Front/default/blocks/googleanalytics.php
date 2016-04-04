<?php
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;

$code = \App::$Properties->get('gaTrackId');
if (!Obj::isString($code) || Str::length($code) < 3) {
    return null;
}
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?= $code ?>', 'auto');
  ga('send', 'pageview');
</script>