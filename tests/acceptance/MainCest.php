<?php

class MainCest
{
    /**
     * Check if main page is available and layout template is rendered
     * @param AcceptanceTester $I
     */
    public function ensureThatMainWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('ffcms');
        $I->seeLink('Sign up');
        $I->seeLink('Sign in');
        $I->see('Find', '#search-submit');
        $I->seeLink('Home');
        $I->seeLink('News');
        $I->seeLink('About');
        $I->seeLink('Feedback');
        $I->seeLink('Users');
    }
}