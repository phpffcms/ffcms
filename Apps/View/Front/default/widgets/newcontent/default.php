<?php
/** @var \Apps\ActiveRecord\Content[] $records */

use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Text;
use Ffcms\Core\Helper\Type\Str;

foreach ($records as $record) {
    $title = \App::$Translate->getLocaleText($record->title);
    if (Str::likeEmpty($title)) {
        continue;
    }
    $title = Text::snippet($title, 50);
    $date = Date::humanize($record->created_at);
    $categoryUrl = \App::$Alias->baseUrl . '/content/list/' . $record->cpath;
    $categoryLink = '<a href="' . $categoryUrl . '">' . \App::$Translate->getLocaleText($record->ctitle) . '</a>';
    $newsLink = \App::$Alias->baseUrl . '/content/read/' . $record->cpath;
    $newsLink = rtrim($newsLink, '/') . '/' . $record->path;

    echo '<div class="row"><div class="col-md-12">';
    echo '<a href="' . $newsLink . '">&rarr; ' . $title . '</a><br />';
    echo '<small class="float-left">' . $categoryLink . '</small>';
    echo '<small class="float-right text-secondary">' . $date . '</small>';
    echo '</div></div>';
    echo '<hr class="pretty" />';
}
