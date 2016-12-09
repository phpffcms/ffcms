<?php

class RecoveryCest
{
    /**
     * Check recovery form with good data
     * @param AcceptanceTester $I
     */
    public function ensureThatRecoverySuccessWork(AcceptanceTester $I)
    {
        $I->amOnPage('/user/recovery');
        $I->see('Recovery form', 'h1');
        $I->fillField('FormRecovery[email]', 'test1@gmail.com');
        $I->fillField('FormRecovery[captcha]', \Helper\Core::getCaptcha());
        $I->click('Make recovery');
        $I->see('We send to you email with instruction to recovery your account', 'p');
    }

    /**
     * Check recovery form with fail data
     * @param AcceptanceTester $I
     */
    public function ensureThatRecoveryFailWork(AcceptanceTester $I)
    {
        $I->amOnPage('/user/recovery');
        $I->see('Recovery form', 'h1');
        $I->fillField('FormRecovery[email]', mt_rand(100, 100000) . '@gmail.com');
        $I->fillField('FormRecovery[captcha]', \Helper\Core::getCaptcha());
        $I->click('Make recovery');
        $I->see('Form validation is failed', 'p');
    }
}