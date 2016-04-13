<?php
/** @var object $records */

use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Helper\Date;

foreach ($records as $record) {
    $title = Serialize::getDecodeLocale($record->title);
    $title = Text::snippet($title, 50);
    $date = Date::convertToDatetime($record->created_at, Date::FORMAT_TO_HOUR);
    $categoryUrl = \App::$Alias->baseUrl . '/content/list/' . $record->cpath;
    $categoryLink = '<a href="' . $categoryUrl . '">' . Serialize::getDecodeLocale($record->ctitle) . '</a>';
    $newsLink = \App::$Alias->baseUrl . '/content/read/' . $record->cpath;
    $newsLink = rtrim($newsLink, '/') . '/' . $record->path;
    
    echo '<div class="row"><div class="col-md-12">';
    echo '<a href="' . $newsLink . '">&rarr; ' . $title . '</a><br />';
    echo '<small class="pull-left">' . $categoryLink . '</small>';
    echo '<small class="pull-right">' . $date . '</small>';
    echo '</div></div>';
    echo '<hr class="pretty" />';
}
?>