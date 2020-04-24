<?php 

class LoginCest
{
    private $login;
    private $password;
    
    public function _before(AcceptanceTester $I)
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('Username', getenv('ADMIN_LOGIN'));
        $I->fillField('Password', getenv('ADMIN_PASSWORD'));
        $I->click('Login');
        $I->see('Выйти');
        $I->saveSessionSnapshot('login');
    }
}
