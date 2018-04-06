<?php
/** @var \Ffcms\Templex\Template\Template $this */
$this->layout('_layouts/default', [
    'title' => 'Developer main page'
])
?>

<?php $this->start('body') ?>

<div class="well well-sm">
    <h1>Developer main page</h1>
    <hr />
</div>

<script>
    $(function(){
        console.log('Use $()');
    });

    $(document).ready(function(){
        console.log('use document');
    })
</script>

<?php $this->stop() ?>