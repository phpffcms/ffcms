<?php

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Url;

/** @var $this object */
/** @var $records Apps\ActiveRecord\UserLog */

$this->title = __('Logs');

$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    Url::to('profile/show', \App::$User->identity()->getId()) => __('Profile'),
    __('Logs')
];

?>

<?= $this->render('profile/_settingsTab') ?>

<h2><?= __('My logs') ?></h2>
<hr />
<?php
if ($records === null || $records->count() < 1) {
    echo __('No logs is available');
    return;
}

$logs = [];
foreach ($records->get() as $log) {
    $logs[] = [
        ['type' => 'text', 'text' => $log->id],
        ['type' => 'text', 'text' => $log->type],
        ['type' => 'text', 'text' => $log->message],
        ['type' => 'text', 'text' => Date::convertToDatetime($log->created_at, Date::FORMAT_TO_HOUR)]
    ];
}
echo Table::display([
    'table' => ['class' => 'table table-striped'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Type')],
            ['text' => __('Message')],
            ['text' => __('Date')]
        ]
    ],
    'tbody' => [
        'items' => $logs
    ]
]);
?>