<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Make authorization
     * @param string $user
     * @param string $password
     * @return int
     */
    public function login($user, $password)
    {
        $I = $this;
        $I->amOnPage('/user/login');
        $I->see('Log In', 'h1');
        $I->fillField('FormLogin[login]', $user);
        $I->fillField('FormLogin[password]', $password);
        $I->click('Do Login');
        $I->see('Account');

        $prefix = \Helper\Core::getConfig('database')['prefix'];

        return (int)$I->grabFromDatabase($prefix . 'users', 'id', ['login' => 'test1']);
    }

    /**
     * Make logout
     */
    public function logout()
    {
        $I = $this;
        $I->amOnPage('/user/logout');
    }

    /**
     * Get user id by login
     * @param string $login
     * @return int
     */
    public function getUserIdByLogin($login)
    {
        $prefix = \Helper\Core::getConfig('database')['prefix'];

        return (int)$this->grabFromDatabase($prefix . 'users', 'id', ['login' => $login]);
    }
}
