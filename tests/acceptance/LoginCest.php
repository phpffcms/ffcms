<?php


class LoginCest
{
    /**
     * Test success authorization data
     * @param AcceptanceTester $I
     */
    public function ensureThatSuccessLoginWork(AcceptanceTester $I)
    {
        $I->amOnPage('/user/login');
        $I->see('Log In', 'h1');
        $I->fillField('FormLogin[login]', 'test1');
        $I->fillField('FormLogin[password]', 'test1');
        $I->click('Do Login');
        $I->see('Account');
    }

    /**
     * Test fail authorization data
     * @param AcceptanceTester $I
     */
    public function ensureThatFailLoginWork(AcceptanceTester $I)
    {
        $I->amOnPage('/user/logout');
        $I->amOnPage('/user/login');
        $I->fillField('FormLogin[login]', mt_rand(100, 10000000));
        $I->fillField('FormLogin[password]', mt_rand(100, 10000000));
        $I->click('Do Login');
        $I->see('User is never exist or password is incorrect!');
    }
}