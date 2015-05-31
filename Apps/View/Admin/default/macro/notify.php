<?php
/** @var $notify array */

use Ffcms\Core\Helper\Object;

/**
 * Get html css class from notify response type
 * @param string $type
 * @return string
 */
function type2html($type)
{
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
        default:
            $htmlType = 'alert-info';
            break;
    }
    return $htmlType;
}

if (Object::isArray($notify) && count($notify) > 0) {
    foreach ($notify as $type => $messages) {
        foreach ($messages as $message) {
            echo '<p class="alert ' . type2html($type) . '">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                . \Ffcms\Core\App::$Security->strip_tags($message) . '</p>';
        }
    }
}
