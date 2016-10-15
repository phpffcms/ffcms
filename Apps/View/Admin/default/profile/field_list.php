<?php

use Ffcms\Core\Helper\HTML\Table;
use Ffcms\Core\Helper\Url;

/** @var $this object */
/** @var $records Apps\ActiveRecord\ProfileField */

$this->title = __('Profile fields');

$this->breadcrumbs = [
    Url::to('main/index') => __('Main'),
    Url::to('application/index') => __('Applications'),
    Url::to('profile/index') => __('Profile list'),
    __('Profile fields')
];

?>

<?= $this->render('profile/_tabs') ?>
<h1><?= __('Additional profile fields') ?></h1>
<hr />
    <div class="row">
        <div class="col-md-12">
            <?= Url::link(['profile/fieldupdate', 0], '<i class="fa fa-plus"></i> ' . __('Add field'), ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>
<?php if ($records->count() > 0):
    $items = [];
    foreach ($records as $row) {
        $labelClass = 'label';
        if ($row->type === 'link') {
            $labelClass .= ' label-primary';
        } else {
            $labelClass .= ' label-default';
        }
        $items[] = [
            ['text' => $row->id],
            ['text' => $row->getLocaled('name')],
            ['text' => '<span class="' . $labelClass . '">' . $row->type . '</span>', 'html' => true],
            ['text' => '<code>' . ($row->reg_cond == 0 ? '!' : null) . 'preg_match("' . $row->reg_exp . '", input)' . '</code>', 'html' => true],
            [
                'text' => Url::link(['profile/fieldupdate', $row->id], '<i class="fa fa-pencil fa-lg"></i> ', ['html' => true]) .
                Url::link(['profile/fielddelete', $row->id], '<i class="fa fa-trash-o fa-lg"></i>', ['html' => true]),
                'html' => true,
                'property' => ['class' => 'text-center']
            ]
        ];
    }
?>

    <?= Table::display([
    'table' => ['class' => 'table table-bordered'],
    'thead' => [
        'titles' => [
            ['text' => 'id'],
            ['text' => __('Title')],
            ['text' => __('Type')],
            ['text' => __('Rule')],
            ['text' => __('Actions')]
        ]
    ],
    'tbody' => [
        'items' => $items
    ]
]);
    ?>
<?php else: ?>
    <p class="alert alert-warning"><?= __('No additional fields is add yet!') ?></p>
<?php endif; ?>