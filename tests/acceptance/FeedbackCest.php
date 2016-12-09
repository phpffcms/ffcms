<?php

use Codeception\Scenario;

class FeedbackCest
{
    /**
     * Test feedback request from guest
     * @param AcceptanceTester $i
     */
    public function ensureThatGuestWork(AcceptanceTester $i)
    {
        // create need feedback_post item
        $i->amOnPage('/feedback/create');
        $i->see('Feedback', 'h1');
        $i->fillField('FormFeedbackAdd[name]', 'Acceptance Tester');
        $i->fillField('FormFeedbackAdd[email]', 'root@ffcms.org');
        $i->fillField('FormFeedbackAdd[message]', 'Hello! I am a acceptance tester and i wanna test this form!');
        $i->fillField('FormFeedbackAdd[captcha]', \Helper\Core::getCaptcha());
        $i->click('Send');
        $i->see('Your message was added successful');
        $i->see('Feedback message', 'h1');
        $i->see('Acceptance Tester (root@ffcms.org)');
        $i->see('Hello! I am a acceptance tester and i wanna test this form!');
        // add new feedback_answer item
        $i->see('Add answer', 'h3');
        $i->fillField('FormAnswerAdd[name]', 'Acceptance Tester');
        $i->fillField('FormAnswerAdd[email]', 'root@ffcms.org');
        $i->fillField('FormAnswerAdd[message]', 'Hello! Now i want to test feedback answers features');
        $i->click('Add');
        $i->see('Answers', 'h3');
        $i->see('Hello! Now i want to test feedback answers features');
    }

    /**
     * Check add new request by authorized user & list added request
     * @param AcceptanceTester $i
     * @param Scenario $scenario
     */
    public function ensureThatAuthorizedWork(AcceptanceTester $i, Scenario $scenario)
    {
        $auth = new AcceptanceTester($scenario);
        $auth->login('test1', 'test1');
        $i->amOnPage('/feedback/create');
        $i->see('New request', 'a');
        $i->see('My requests', 'a');
        $i->fillField('FormFeedbackAdd[name]', 'Acceptance Tester');
        $i->fillField('FormFeedbackAdd[email]', 'root@ffcms.org');
        $i->fillField('FormFeedbackAdd[message]', 'Hello! I am a authorized user and i want to create new feedback request!');
        $i->fillField('FormFeedbackAdd[captcha]', \Helper\Core::getCaptcha());
        $i->click('Send');
        $i->see('Your message was added successful');
        $i->see('Close request', 'a');
        $i->amOnPage('/feedback/list');
        $i->see('Message');
        $i->see('Status');
        $i->see('Hello! I am a authorized', 'a');

    }
}