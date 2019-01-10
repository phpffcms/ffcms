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
        $i->fillField('input[name=query]', 'ffcms');
        // check post response
        $i->click('Search');
        $i->see('Search query: ffcms', 'h1');
        $i->see('writed on php language syntax and using');
    }
}