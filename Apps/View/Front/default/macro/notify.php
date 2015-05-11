<?php
use Ffcms\Core\Helper\Object;

function type2html($type)
{
    $htmlType = 'alert-info';
    switch ($type) {
        case 'error':
            $htmlType = 'alert-danger';
            break;
        case 'success':
            $htmlType = 'alert-success';
            break;
        case 'warning':
            $htmlType = 'alert-warning';
            break;
    }
    return $htmlType;
}

if (($type === null || $text === null)) {
    if (!Object::isArray($object)) {
        echo '<p>Variable $type or $message is undefined and $object not resended!</p>';
    } else {
        foreach ($object as $row) {
            echo '<p class="alert ' . type2html($row['type']) . '">' . \App::$Security->strip_tags($row['text']) . '</p>';
        }
    }
} else {
    echo '<p class="alert ' . type2html($type) . '">' . \App::$Security->strip_tags($text) . '</p>';
}