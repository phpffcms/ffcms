<?php

namespace Apps\Controller\Front\Content;

use Apps\ActiveRecord\Content as ContentRecord;
use Apps\ActiveRecord\ContentCategory;
use Apps\Model\Front\Sitemap\EntityBuildMap;
use Ffcms\Core\App;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;


/**
 * Trait ScheduledActions
 * @package Apps\Controller\Front\Content
 */
trait ScheduledActions
{
    public static $updateSitemapDelay = 120; // sitemap update delay in minutes, 120 = 2h
    public static $contentRowsEachRun = 5000; // content count per each one iteration

    /** Cron schedule action - build content sitemap
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @return void
     */
    public static function sitemap(): void
    {
        // get records from database as activerecord object
        $contents = ContentRecord::with('category')
            ->where('display', '=', 1);
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
        $iterations = (int)($contentCount / self::$contentRowsEachRun);
        for ($i = 0; $i <= $iterations; $i++) {
            // check if lifetime is expired for current sitemap index
            $xmlTime = File::mTime('/upload/sitemap/content.' . $i . '.' . $langs[0] . '.xml');
            $updateDelay = self::$updateSitemapDelay * 60;
            $updateDelay += mt_rand(0, 1800); // +- 0-30 rand min for caching update
            // do not process if cache time is not expired
            if (time() - $xmlTime <= $updateDelay) {
                continue;
            }

            // get results with current offset
            $offset = $i * self::$contentRowsEachRun;
            $result = $contents->take(self::$contentRowsEachRun)
                ->skip($offset)
                ->get();

            // build sitemap from content items via business model
            $sitemap = new EntityBuildMap($langs);
            foreach ($result as $content) {
                /** @var \Apps\ActiveRecord\Content $content */
                $uri = '/content/read/';
                if (!Str::likeEmpty($content->category->path)) {
                    $uri .= $content->category->path . '/';
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