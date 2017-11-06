<?php

class RegisterCest
{
    public function ensureThatRegistrationWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/user/signup');
        $I->see('Sign up', 'h1');
        $I->fillField('FormRegister[login]', 'test3');
        $I->fillField('FormRegister[email]', 'test3@gmail.com');
        $I->fillField('FormRegister[password]', 'test3');
        $I->fillField('FormRegister[repassword]', 'test3');
        $I->fillField('FormRegister[captcha]', \Helper\Core::getCaptcha());
        $I->click('FormRegister[submit]');
        $I->wait(1);
        $I->see('Your account is registered. You must confirm account via email');
    }
}