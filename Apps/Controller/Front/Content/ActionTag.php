<?php

namespace Apps\Controller\Front\Content;

use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\Content as ContentRecord;

/**
 * Trait ActionTag
 * @package Apps\Controller\Front\Content
 * @property View $view
 * @property Request $request
 * @property Response $response
 * @method array getConfigs
 */
trait ActionTag
{
    /**
     * List latest by created_at content items contains tag name
     * @param string $name
     * @return null|string
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function tag($name): ?string
    {
        // remove spaces and other sh@ts
        $name = App::$Security->strip_tags(trim($name));

        // check if tag is not empty
        if (!Any::isStr($name) || Str::length($name) < 2) {
            throw new NotFoundException(__('Tag is empty or is too short!'));
        }

        // get equal rows order by creation date
        $records = ContentRecord::where('meta_keywords', 'like', '%' . $name . '%')
            ->orderBy('created_at', 'DESC')
            ->take(self::TAG_PER_PAGE);
        // check if result is not empty
        if ($records->count() < 1) {
            throw new NotFoundException(__('Nothing founded'));
        }

        // define tag list event
        App::$Event->run(static::EVENT_TAG_LIST, [
            'records' => $records
        ]);

        // render response
        return $this->view->render('tag', [
            'records' => $records->get(),
            'tag' => $name
        ]);
    }
}
