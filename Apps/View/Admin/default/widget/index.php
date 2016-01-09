<?php

/** @var $this object */
/** @var $widgets object */
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

$this->title = __('Widgets');
$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    __('Widgets')
];
?>
    <h1><?= __('Widgets list'); ?></h1>
    <hr/>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Url::link(['widget/install'], '<i class="fa fa-tasks"></i> ' . __('Install'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
<?php
$widgetTableItems = null;
foreach ($widgets as $widget) {
    /** @var $widget Apps\ActiveRecord\App */
    $route = $widget->sys_name . '/index';
    $icoStatus = null;
    $actions = $this->render('macro/widget_actions', ['controller' => $widget->sys_name]);
    if ((int)$widget->disabled !== 0) {
        $icoStatus = ' <i class="fa fa-pause" style="color: #ff0000;"></i>';
    } elseif ($widget->checkVersion() !== true) {
        $icoStatus = ' <i class="fa fa-exclamation" style="color: #ffbd26;"></i>';
        $actions = Url::link(['widget/update', $widget->sys_name], '<i class="fa fa-wrench"></i>');
    } else {
        $icoStatus = ' <i class="fa fa-check" style="color: #008000;"></i>';
    }

    $widgetTableItems[] = [
        ['text' => $widget->id . $icoStatus, 'html' => true, '!secure' => true],
        ['text' => Url::link([$route], $widget->getLocaleName()), 'html' => true],
        ['text' => $widget->version],
        ['text' => Date::convertToDatetime($widget->updated_at, Date::FORMAT_TO_HOUR)],
        ['text' => $actions, 'property' => ['class' => 'text-center'], 'html' => true]
    ];
}

?>

<?php if ($widgetTableItems === null || count($widgetTableItems) < 1) {
    echo '<p class="alert alert-info">' . __('Installed widgets is not founded') . '</p>';
} ?>


<?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => '#'],
            ['text' => __('Widget')],
            ['text' => __('Version')],
            ['text' => __('Activity')],
            ['text' => __('Actions')]
        ]
    ],
    'tbody' => [
        'items' => $widgetTableItems
    ]
]); ?>