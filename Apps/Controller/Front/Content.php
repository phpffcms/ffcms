<?php

namespace Apps\Controller\Front;

use Extend\Core\Arch\FrontAppController;

/**
 * Class Content. Controller of content app - content and categories.
 * @package Apps\Controller\Front
 */
class Content extends FrontAppController
{
    const TAG_PER_PAGE = 50;

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

    use Content\ActionComments {
        comments as actionComments;
    }

    use Content\ScheduledActions {
        sitemap as buildSitemapSchedule;
    }
}
