<?php

use Codeception\Scenario;


class LoginCest
{
    /**
     * Test success authorization data
     * @param AcceptanceTester $i
     * @param Scenario $scenario
     */
    public function ensureThatSuccessLoginWork(AcceptanceTester $i, Scenario $scenario)
    {
        $auth = new AcceptanceTester($scenario);
        $auth->login('test1', 'test1');
    }

    /**
     * Test fail authorization data
     * @param AcceptanceTester $i
     * @param Scenario $scenario
     */
    public function ensureThatFailLoginWork(AcceptanceTester $i, Scenario $scenario)
    {
        $i->amOnPage('/user/logout');
        $i->amOnPage('/user/login');
        $i->fillField('FormLogin[login]', mt_rand(100, 10000000));
        $i->fillField('FormLogin[password]', mt_rand(100, 10000000));
        $i->click('Do Login');
        $i->see('User is never exist or password is incorrect!');
    }
}