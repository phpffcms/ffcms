<?php

class ContentCest
{
    /**
     * Check is category available to read
     * @param AcceptanceTester $i
     */
    public function ensureThatCategoryReadWorks(AcceptanceTester $i)
    {
        $i->amOnPage('/content/list/news');
        $i->see('FFCMS 3 - the content management system', 'a');
        $i->see('FFCMS 3 - the new version of ffcms', 'p');
    }

    /**
     * Check is content item available to read
     * @param AcceptanceTester $i
     */
    public function ensureThatContentReadWorks(AcceptanceTester $i)
    {
        $i->amOnPage('/content/read/news/ffcms3-announce');
        $i->see('FFCMS 3 - the content management system', 'h1');
        $i->see('The FFCMS system can be used in any kind of', 'p');
    }
}