<?php

use Codeception\Scenario;

class CommentCest
{
    /**
     * Check comment sending
     * @param AcceptanceTester $i
     * @param Scenario $scenario
     */
    public function ensureCommentsWork(AcceptanceTester $i, Scenario $scenario)
    {
        $auth = new AcceptanceTester($scenario);
        $auth->login('test1', 'test1');

        $i->amOnPage('/content/read/news/ffcms3-announce');
        $i->scrollTo('div.h3');
        $i->wait(1);
        $i->see('Comments', 'div.h3');
        //$i->fillField('message', 'New comment from codeception tester');
        $i->executeJS('$(\'textarea[name=message]\').val(\'New comment from codeception tester\');');
        $i->wait(1);
        $i->click('Send');
        $i->wait(1);
        $i->see('New comment from codeception tester');
    }
}