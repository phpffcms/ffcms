<?php


namespace Apps\Controller\Front\Content;

use Apps\Model\Front\Content\EntityCategoryList;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

/**
 * Trait ActionRss
 * @package Apps\Controller\Front\Content
 * @property View $view
 * @property Request $request
 * @property Response $response
 * @method array getConfigs
 */
trait ActionRss
{
    /**
     * Display rss feeds from content category
     * @return string
     * @throws ForbiddenException
     */
    public function rss(): ?string
    {
        $path = $this->request->getPathWithoutControllerAction();
        $configs = $this->getConfigs();

        // build model data
        $model = new EntityCategoryList($path, $configs, 0);
        // remove global layout
        $this->layout = null;

        // check if rss display allowed for this category
        if ((int)$model->category['configs']['showRss'] !== 1) {
            throw new ForbiddenException(__('Rss feed is disabled for this category'));
        }

        // set rss/xml header
        $this->response->headers->set('Content-Type', 'application/rss+xml');

        // initialize rss feed objects
        $feed = new Feed();
        $channel = new Channel();

        // set channel data
        $channel->title($model->category['title'])
            ->description($model->category['description'])
            ->url(App::$Alias->baseUrl . '/content/list/' . $model->category['path'])
            ->appendTo($feed);

        // add content data
        if ($model->getContentCount() > 0) {
            foreach ($model->items as $row) {
                $item = new Item();
                // add title, short text, url
                $item->title($row['title'])
                    ->description($row['text'])
                    ->url(App::$Alias->baseUrl . $row['uri']);
                // add poster
                if ($row['thumb'] !== null) {
                    $item->enclosure(App::$Alias->scriptUrl . $row['thumb'], $row['thumbSize'], 'image/jpeg');
                }

                // append response to channel
                $item->appendTo($channel);
            }
        }
        // define rss read event
        App::$Event->run(static::EVENT_RSS_READ, [
            'model' => $model,
            'feed' => $feed,
            'channel' => $channel
        ]);

        // render response from feed object
        return $feed->render();
    }
}
