<?php


use Codeception\Scenario;

class ProfileCest
{
    /**
     * Check profile is exists and add wall post
     * @param AcceptanceTester $i
     * @param Scenario $scenario
     */
    public function ensureProfileIsWork(AcceptanceTester $i, Scenario $scenario)
    {
        $auth = new AcceptanceTester($scenario);
        $userId = $auth->login('test1', 'test1');

        $i->amOnPage('/profile/show/' . $userId);
        $i->see('Profile data', 'h2');
        $i->see('Settings', 'a');

        $i->executeJS('$(\'textarea[id=FormWallPost-message]\').val(\'Hello! I am a codecept test and i wanna test wall!\');');
        $i->wait(1);
        $i->click('Send');

        $i->see('The message was successful posted!');
        $i->see('Hello! I am a codecept test and i wanna test wall!');
    }

    /**
     * Test profile settings and display changes after save
     * @param AcceptanceTester $i
     * @param Scenario $scenario
     */
    public function ensureSettingsIsWork(AcceptanceTester $i, Scenario $scenario)
    {
        $auth = new AcceptanceTester($scenario);
        $userId = $auth->login('test1', 'test1');

        $i->amOnPage('/profile/settings');
        $i->fillField('FormSettings[name]', 'Codeception 1');
        $i->selectOption('FormSettings[sex]', 'Male');
        $i->fillField('FormSettings[birthday]', '1.1.1970');
        $i->fillField('FormSettings[city]', 'Moscow');
        $i->fillField('FormSettings[hobby]', 'tdd, bdd, tests');
        $i->fillField('FormSettings[phone]', '+71234567890');
        $i->fillField('FormSettings[url]', 'https://ffcms.org');
        $i->click('Save');

        $i->see('Profile data are successful updated');
        $i->wantTo('Check saved profile data');

        $i->amOnPage('/profile/show/' . $userId);
        $i->see('Codeception 1', 'h1');
        $i->see('01.01.1970', 'a');
        $i->see('Male', 'td');
        $i->see('+71234567890');
        $i->see('Moscow', 'a');
        $i->see('tdd', 'a');
        $i->see('bdd', 'a');
        $i->see('tests', 'a');
    }

    /**
     * Test change password settings features
     * @param AcceptanceTester $i
     * @param Scenario $scenario
     */
    public function ensurePasswordChangeIsWork(AcceptanceTester $i, Scenario $scenario)
    {
        $auth = new AcceptanceTester($scenario);
        $auth->login('test1', 'test1');

        // set new password
        $i->amOnPage('/profile/password');
        $i->fillField('FormPasswordChange[current]', 'test1');
        $i->fillField('FormPasswordChange[new]', 'test123');
        $i->fillField('FormPasswordChange[renew]', 'test123');
        $i->click('Update');

        $i->see('Password is successful changed', 'div');
        $auth->logout();
        // check new password
        $auth->login('test1', 'test123');

        // recovery old password
        $i->amOnPage('/profile/password');
        $i->fillField('FormPasswordChange[current]', 'test123');
        $i->fillField('FormPasswordChange[new]', 'test1');
        $i->fillField('FormPasswordChange[renew]', 'test1');
        $i->click('Update');
        $i->see('Password is successful changed', 'div');
        $auth->logout();
    }

    /**
     * Test personal messages dialog
     * @param AcceptanceTester $i
     * @param Scenario $scenario
     */
    public function ensureMessagesIsWork(AcceptanceTester $i, Scenario $scenario)
    {
        // init aut and authorize 1st user (test1)
        $auth = new AcceptanceTester($scenario);
        $test2Id = $auth->getUserIdByLogin('test2');
        $test1Id = $auth->login('test1', 'test1');

        // go to new dialog
        $i->amOnPage('/profile/messages?newdialog=' . $test2Id);
        $i->wait(1);

        // write message from 1st user to 2nd user (test1 -> test2)
        $i->fillField('#msg-text', 'Hello! This is a test msg from codecept test');
        $i->click('//*[@id="send-new-message"]');
        $i->wait(1);

        // check if msg is available to see
        $i->see('You', '#msg-user-nick');
        $i->see('Hello! This is a test msg from codecept test');

        // logout from 1st user (test1)
        $auth->logout();
        // authorize to 2nd user (test2)
        $auth->login('test2', 'test2');

        // load dialogs
        $i->amOnPage('/profile/messages');
        $i->wait(1);
        $i->click('//*[@id="msg-user-' . $test1Id . '"]');
        $i->wait(1);

        /// check if msg from 1st user is available
        $i->see('Hello! This is a test msg from codecept test');
        // send new message from 2nd to 1st (test2->test1)
        $i->fillField('#msg-text', 'Hello my friend! I am a 2nd codecept test user!');
        $i->click('//*[@id="send-new-message"]');

        // check if msg test2->test1 is readable
        $i->wait(1);
        $i->see('Hello my friend! I am a 2nd codecept test user!');
    }
}