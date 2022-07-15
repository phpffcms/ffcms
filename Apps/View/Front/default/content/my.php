<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;
use Ffcms\Templex\Helper\Html\Dom;

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\Content[] $records */
/** @var array $pagination */

$this->layout('_layouts/default', [
    'title' => __('My content'),
    'breadcrumbs' => [
        Url::to('/') => __('Home'),
        Url::to('content/index') => __('Contents'),
        __('My content')
    ]
]);

?>
<?php $this->start('body') ?>
<h1><?= __('My content')?></h1>
<hr />
<?php
if (!$records || $records->count() < 1) {
    $this->bootstrap()->alert('warning', __('Content is not found yet'));
    $this->stop();
    return;
}
?>

<p><?= __('Remember you can edit content only on moderate stage!') ?></p>
<?= Url::a(['content/update'], __('Add news'), ['class' => 'btn btn-primary']); ?>

<?php
$table = $this->table(['class' => 'table table-striped w-100'])
    ->head([
        ['text' => '#'],
        ['text' => __('Title')],
        ['text' => __('Published')],
        ['text' => __('Date')]
    ]);

foreach ($records as $record) {
    $moderate = !(bool)$record->display;
    $title = $record->getLocaled('title');
    if (!(bool)$record->display) {
        $title = Url::a(['content/update', [$record->id]], $title) . ' <i class="fas fa-pencil-alt"></i>';
    }
    //$newsLink = \App::$Alias->baseUrl . '/content/read/' . $record->cpath;
    //$newsLink = rtrim($newsLink, '/') . '/' . $record->path;

    $table->row([
        ['text' => $record->id],
        ['text' => $title, 'html' => true],
        ['text' => $moderate ? __('No') : __('Yes'), 'properties' => ['class' => $moderate ? 'text-danger' : 'text-success']],
        ['text' => Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR)]
    ]);
}
?>

<div class="table-responsive">
    <?= $table->display() ?>
</div>

<?= $this->bootstrap()->pagination(['content/my'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display(); ?>

<?= Url::a(['content/update'], __('Add content'), ['class' => 'btn btn-primary']) ?>

<?php $this->stop() ?>
