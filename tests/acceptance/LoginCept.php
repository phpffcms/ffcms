<?php

$I = new AcceptanceTester($scenario);
$I->wantToTest('do login');
$I->amOnPage('/user/login');
$I->fillField('FormLogin[login]', 'test');
$I->fillField('FormLogin[password]', 'test');
$I->click('Do Login');
$I->see('Profile', 'a');