<?php
use Ffcms\Core\Helper\Url;

/** @var \Apps\ActiveRecord\ContentTag $records */
?>

<?php foreach ($records as $row) {
    echo Url::link(['content/tag', $row['tag']], $row['tag'], ['class' => 'label label-default']) . ' ';
}