<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\Content as ContentRecord;
use Apps\ActiveRecord\ContentCategory;
use Apps\Model\Front\Sitemap\EntityBuildMap;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class Content. Controller of content app - content and categories.
 * @package Apps\Controller\Front
 */
class Content extends FrontAppController
{
    const TAG_PER_PAGE = 50;
    const SITEMAP_UPDATE_DELAY = 120; // sitemap update delay in minutes, 120 = 2h
    const SITEMAP_CONTENT_COUNT_ITERATION = 5000; // content count per each one iteration

    const EVENT_CONTENT_READ = 'content.read';
    const EVENT_RSS_READ = 'content.rss.read';
    const EVENT_CONTENT_LIST = 'content.list';
    const EVENT_TAG_LIST = 'content.tags';

    /**
     * Fatty action like actionList(), actionRead() are located in standalone traits.
     * This feature allow provide better read&write accessibility
     */

    use Content\ActionList {
        listing as actionList;
    }

    use Content\ActionRead {
        read as actionRead;
    }

    use Content\ActionUpdate {
        update as actionUpdate;
    }

    use Content\ActionTag {
        tag as actionTag;
    }

    use Content\ActionRss {
        rss as actionRss;
    }

    use Content\ActionMy {
        my as actionMy;
    }

    /**
     * Cron schedule action - build content sitemap
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public static function buildSitemapSchedule()
    {
        // get records from database as activerecord object
        $contents = ContentRecord::where('display', '=', 1);
        $contentCount = $contents->count();
        if ($contentCount < 1) {
            return;
        }

        // get languages if multilanguage enabled
        $langs = null;
        if (App::$Properties->get('multiLanguage')) {
            $langs = App::$Properties->get('languages');
        }

        // build sitemap items using iteration - 5000 rows per each one
        $iterations = (int)($contentCount / static::SITEMAP_CONTENT_COUNT_ITERATION);
        for ($i = 0; $i <= $iterations; $i++) {
            // check if lifetime is expired for current sitemap index
            $xmlTime = File::mTime('/upload/sitemap/content.' . $i . '.' . $langs[0] . '.xml');
            $updateDelay = static::SITEMAP_UPDATE_DELAY * 60;
            $updateDelay += mt_rand(0, 1800); // +- 0-30 rand min for caching update
            // do not process if cache time is not expired
            if (time() - $xmlTime <= $updateDelay) {
                continue;
            }

            // get results with current offset
            $offset = $i * static::SITEMAP_CONTENT_COUNT_ITERATION;
            $result = $contents->take(static::SITEMAP_CONTENT_COUNT_ITERATION)->skip($offset)->get();

            // build sitemap from content items via business model
            $sitemap = new EntityBuildMap($langs);
            foreach ($result as $content) {
                $category = $content->getCategory();
                $uri = '/content/read/';
                if (!Str::likeEmpty($category->path)) {
                    $uri .= $category->path . '/';
                }
                $uri .= $content->path;
                $sitemap->add($uri, $content->created_at, 'weekly', 0.7);
            }
            // add categories
            $categories = ContentCategory::all();
            foreach ($categories as $item) {
                if ((bool)$item->getProperty('showCategory')) {
                    $uri = '/content/list/' . $item->path;
                    $sitemap->add($uri, date('c'), 'daily', 0.9);
                }
            }
            // save data to xml file
            $sitemap->save('content.' . $i);
        }
    }
}
