<?php

$I = new AcceptanceTester($scenario);
$I->wantToTest('main page');
$I->amOnPage('/');
$I->see('ffcms');
$I->seeLink('Sign up');
$I->seeLink('Sign in');