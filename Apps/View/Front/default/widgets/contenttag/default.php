<?php

use Ffcms\Templex\Url\Url;

/** @var \Apps\ActiveRecord\ContentTag $records */

?>

<?php foreach ($records as $row) {
    echo Url::a(['content/tag', [$row['tag']]], $row['tag'], ['class' => 'badge badge-secondary']) . ' ';
}