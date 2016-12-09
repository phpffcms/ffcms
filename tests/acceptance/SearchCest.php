<?php

class SearchCest
{
    /**
     * Test search app
     * @param AcceptanceTester $i
     */
    public function ensureThatSearchWork(AcceptanceTester $i)
    {
        // check ajax response
        $i->amOnPage('/undefined');
        $i->fillField('query', 'ffcms');
        $i->wait(1); // wait for ajax response onKeyDown
        $i->see('FFCMS 3 - the');
        // check post response
        $i->click('//button[@id="search-submit"]');
        $i->see('Search query: ffcms', 'h1');
        $i->see('ffcms writed on php language syntax and using');
    }
}