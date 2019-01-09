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

<?= \Ffcms\Core\Helper\Security::password_hash('d7k1p9j2') ?><br />
<?= \Ffcms\Core\Helper\Crypt::passwordHash('d7k1p9j2') ?>

<script>
    $(function(){
        console.log('Use $()');
    });

    $(document).ready(function(){
        console.log('use document');
    })
</script>

<?php $this->stop() ?>